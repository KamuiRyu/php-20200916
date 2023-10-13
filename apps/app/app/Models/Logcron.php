<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logcron extends Model
{
    use HasFactory;

    public static function saveLog($data)
    {
        try {
            self::insert($data);
            return ['success' => true, 'message' => 'Log gravado com sucesso'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro ao gravar o log: ' . $e->getMessage()];
        }
    }

    public static function getLastCron()
    {
        try {
            $lastCron = self::orderBy('startTime', 'desc')->first();

            if ($lastCron) {
                return [
                    'lastSync' => $lastCron->startTime,
                    'response' => $lastCron->response,
                ];
            } else {
                return null;
            }
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => 'Erro ao buscar o último CRON: ' . $th->getMessage()];
        }
    }
}
