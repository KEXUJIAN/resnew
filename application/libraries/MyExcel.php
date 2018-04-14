<?php

namespace Res\Util;

/**
 * Excel handler
 */
class MyExcel
{
    const ERROR_CODE_SUCCESS    = 0;
    const ERROR_CODE_NO_HEAD    = 1;
    const ERROR_CODE_BAD_FORMAT = 2;
    const ERROR                 = [
        0 => 'Success',
        1 => '无法定位到表头',
        2 => '不支持的表格格式',
    ];

    public function __construct()
    {
        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php_excel_cache' . DIRECTORY_SEPARATOR;
        if (!is_dir($tmp)) {
            mkdir($tmp, 0777);
        }
        $cacheMethod   = \PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
        $cacheSettings = ['dir' => $tmp];
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
    }

    public function load(string $path, array &$headPatterns)
    {
        $type   = \PHPExcel_IOFactory::identify($path);
        $reader = \PHPExcel_IOFactory::createReader($type);
        $reader->setReadDataOnly(true);
        $excel = $reader->load($path);

        $sheet  = $excel->getSheet(0);
        $maxRow = $sheet->getHighestRow();
        $result = [
            'result'  => true,
            'message' => self::ERROR[self::ERROR_CODE_SUCCESS],
            'code'    => self::ERROR_CODE_SUCCESS,
        ];

        $allHeads = [];
        foreach ($headPatterns as $key => $patterns) {
            $allHeads = array_merge($allHeads, $patterns);
        }
        $headRow = 0;
        for ($i = 1; $i <= $maxRow; ++$i) {
            $maxCol = \PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
            $isHead = false;
            for ($j = 0; $j < $maxCol; ++$j) {
                $val = (string) $sheet->getCellByColumnAndRow($j, $i)->getValue();
                if (!$val) {
                    continue;
                }
                foreach ($allHeads as $headPattern) {
                    if (preg_match($headPattern, $val)) {
                        $isHead  = true;
                        $headRow = $i;
                        break;
                    }
                }
                if ($isHead) {
                    break;
                }
            }
            if ($isHead) {
                break;
            }
        }
        unset($allHeads);

        if (!$headRow) {
            $result['result']  = false;
            $result['message'] = self::ERROR[self::ERROR_CODE_NO_HEAD];
            $result['code']    = self::ERROR_CODE_NO_HEAD;
            $sheet->disconnectCells();
            $excel->disconnectWorksheets();
            unset($heet, $excel);
            return $result;
        }

        $head   = [];
        $maxCol = \PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn($headRow));
        for ($col = 0; $col < $maxCol; ++$col) {
            $val = (string) $sheet->getCellByColumnAndRow($col, $headRow)->getValue();
            if (!$val) {
                continue;
            }
            $find = false;
            foreach ($headPatterns as $name => $patterns) {
                foreach ($patterns as $pattern) {
                    if (!preg_match($pattern, $val)) {
                        continue;
                    }
                    $head[$name] = $col;
                    $find        = true;
                    break;
                }
                if ($find) {
                    break;
                }
            }
            if (count($head) === count($headPatterns)) {
                break;
            }
        }
        if (count($head) !== count($headPatterns)) {
            $result['result']  = false;
            $result['message'] = self::ERROR[self::ERROR_CODE_BAD_FORMAT];
            $result['code']    = self::ERROR_CODE_BAD_FORMAT;
            $sheet->disconnectCells();
            $excel->disconnectWorksheets();
            unset($sheet, $excel);
            return $result;
        }
        $diff = array_diff_key($headPatterns, $head);
        if ($diff) {
            foreach ($diff as $name => $val) {
                $head[$name] = -1;
            }
        }

        $content = [];
        for ($i = $headRow + 1; $i <= $maxRow; ++$i) {
            $row = [];
            foreach ($head as $name => $col) {
                if ($col === -1) {
                    $row[$name] = [
                        'val' => '',
                        'col' => -1,
                        'row' => -1,
                    ];
                    continue;
                }
                $colValue   = trim((string) $sheet->getCellByColumnAndRow($col, $i)->getValue());
                $row[$name] = [
                    'value' => $colValue,
                    'col'   => $col,
                    'row'   => $i,
                ];
            }
            $allEmpty = true;
            foreach ($row as $def) {
                if ('' !== $def['value']) {
                    $allEmpty = false;
                    break;
                }
            }
            if ($allEmpty) {
                continue;
            }
            $content[] = $row;
        }
        $result['content'] = $content;
        $sheet->disconnectCells();
        $excel->disconnectWorksheets();
        unset($sheet, $excel);
        return $result;
    }
}

