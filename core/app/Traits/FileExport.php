<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

trait FileExport
{

    /**
     * Contains the file name like all_contact.csv
     */
    public $fileName;

    /**
     * Which columns to be exported
     *
     */
    public $exportColumns;


    /**
     * How many rows want to export from database
     *
     */
    public $exportItem;

    /**
     * Data ordering
     *
     */
    public $orderBy;

    /**
     * Data to export
     * @var object $data
     */
    public $data = null;


    public  function export()
    {
        $modelName = get_class();
        $columns   = $this->getColumNames();


        if(!$this->data){
            if ($this->exportColumns) {
                if (auth()->guard('admin')->check()) {
                    $data = $modelName::orderBy('id', $this->orderBy)->take($this->exportItem);
                } else {
                    $data = $modelName::orderBy('id', $this->orderBy)->where('user_id', auth()->id())->take($this->exportItem);
                }
                $data = $data->select($this->exportColumns)->get();
            } else {
                if (auth()->guard('admin')->check()) {
                    $data = $modelName::select('*')->get();
                } else {
                    $data = $modelName::where('user_id', auth()->id())->select('*')->get();
                }
            }
        }else{
            $data = $this->data;
        }

        if ($data->count() <= 0) {
            $notify[] = ['warning', 'No data found'];
            return back()->withNotify($notify);
        }

        if ($this->exportColumns) {
            $columns = array_intersect($columns, $this->exportColumns);
        }

        $fileName = "assets/export_file/" . $this->fileName;
        $fp       = fopen($fileName, 'w');
        fputcsv($fp, $columns);
        foreach ($data as $item) {

            fputcsv($fp, $item->toArray());
        }

        fclose($fp);
        return response()->download($fileName);
    }

    public static function getColumNames()
    {
        $modelName = get_class();
        $tableName = app($modelName)->getTable();
        $columns   = Schema::getColumnListing($tableName);
        $columns = array_diff($columns, ['id', 'user_id', 'plan_id', 'status', 'created_at', 'updated_at']);

        return $columns;
    }
}
