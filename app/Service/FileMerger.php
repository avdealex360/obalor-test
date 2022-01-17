<?php

namespace App\Service;

use App\Customers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class FileMerger
{
    public $filename;
    public $lastError;
    public $lastResult;

    protected $file;

    const LOCATION = 'examples';
    const LOCATION_LOG = 'storage';

    /**
     * Читаем файл с помощью генератора, во избежании ошибок с гигантскими файлами
     * @return \Generator|void
     */
    public function readRow()
    {
        while (!feof($this->file)) {
            $row = fgetcsv($this->file);

            yield $row;
        }

        return;
    }

    /**
     * Основная функция импорта сsv
     */
    public function merge()
    {
        $filePath = public_path(self::LOCATION . "/" . $this->filename);

        try {
            $this->file = fopen($filePath, 'r');
        } catch (\Exception $e) {
            $this->lastResult = 'Файл '.$filePath.' не найден';
            return false;
        }

        $i = 0;
        $errorCounter = 0;

        //удаляем лог и чистим таблицу
        $this->deleteErrorLog();
        $this->truncuateCustomers();

        foreach ($this->readRow() as $row) {

            //пропускаем заголовки
            if ($i == 0) {
                $i++;
                continue;
            }

            //парсим строку из файла по шаблону из условия задания (в ТЗ необычный формат СSV, из-за чего fgetcsv не может разобрать столбцы на элементы)
            $customerRaw = explode(',', $row[0]);

            if ($customerRaw[0] == null) {
                break;
            }

            //чистим и приводим к типам
            $customerData = [
                'name' => (string)trim($customerRaw[1]),
                'email' => (string)trim($customerRaw[2]),
                'age' => (int)preg_replace('/[^0-9]/', '', $customerRaw[3]),
                'location' => (string)trim($customerRaw[4]),
            ];

            //валидируем
            $validatator = Validator::make($customerData, [
                'name' => 'required',
                'age' => 'required|numeric|between:18,99',
                'email' => 'required|email',
                'location' => 'required|string',
            ]);

            $this->lastError = [];

            if ($validatator->fails()) {
                $messages = $validatator->errors()->messages();

                if (count($messages) == 1 && isset($messages['location'])) {
                    //если ошибка только в локации, заменяем и продолжаем
                    $customerData['location'] = 'Unknown';

                } else {
                    //иначе записываем в лог
                    $errorCounter++;

                    foreach ($messages as $key => $value) {
                        $this->lastError['additional'][] = $key;
                    }

                    $this->lastError['row'] = $row[0];

                    $this->writeErrorLog();

                    $i++;
                    continue;
                }
            }

            //cоздаем запись в бд
            $this->createCustomer($customerData);
            $i++;
        }

        if ($errorCounter > 0) {
            $this->lastResult = 'Были найдены ошибки валидации, отчет доступен по ссылке: ' . asset('storage/' . 'errors_' . $this->filename) . ' или напрямую ' . public_path(self::LOCATION_LOG . "/" . 'errors_' . $this->filename);
            return false;
        } else {
            $this->lastResult = 'Все записи импортированы успешно';
            return true;
        }
    }

    /**
     * Удаление лога
     */
    public function deleteErrorLog()
    {
        Storage::disk('public')->delete('errors_' . $this->filename);
    }

    /**
     * Запись очередной ошибки в текущий лог (С учетом оригинального "особого" форматирования CSV из технического задания)
     */
    public function writeErrorLog()
    {
        $path = public_path(self::LOCATION_LOG . "/" . 'errors_' . $this->filename);
        $data = explode(',', $this->lastError['row']);

        if (!file_exists($path)) {
            $file = fopen($path, 'w');
            fputcsv($file, ['id, name, email, age, location, error']);
        } else {
            $file = fopen($path, 'a');
        }

        $errorFields = '';

        foreach ($this->lastError['additional'] as $error) {
            $errorFields .= $error . ' ';
        }

        fputcsv($file, [$data[0] . ',' . $data[1] . ',' . $data[2] . ',' . $data[3] . ',' . trim($data[4]) . ',' . trim($errorFields)]);

        fclose($file);
    }

    /**
     * Создание записи в бд на основе провалидированных данных
     * @param $customerData
     * @return mixed
     */
    public function createCustomer($customerData)
    {
        return Customers::create([
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'age' => $customerData['age'],
            'location' => $customerData['location'],
        ]);
    }


    /**
     * Очищаем таблицу модели
     */
    public function truncuateCustomers()
    {
        $customer = new Customers();
        DB::table($customer->getTable())->truncate();
    }
}