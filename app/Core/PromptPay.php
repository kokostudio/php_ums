<?php

namespace App\Core;

class PromptPay
{
    public static function generatePayload(string $target, ?float $amount = null): string
    {
        $target = preg_replace('/[^0-9]/', '', $target);

        if (strlen($target) === 10 && substr($target, 0, 1) === '0') {
            $formattedTarget = '0066' . substr($target, 1);
            $type = '01';
        } else {
            $formattedTarget = str_pad($target, 13, '0', STR_PAD_LEFT);
            $type = '02';
        }

        $tag29 = '0016A000000677010111' . $type . sprintf('%02d', strlen($formattedTarget)) . $formattedTarget;
        $tag29Block = '29' . sprintf('%02d', strlen($tag29)) . $tag29;

        $payload = '000201';
        $payload .= ($amount !== null && $amount > 0) ? '010212' : '010211';
        $payload .= $tag29Block;
        $payload .= '5303764'; // THB currency

        if ($amount !== null && $amount > 0) {
            $amountStr = number_format($amount, 2, '.', '');
            $payload .= '54' . sprintf('%02d', strlen($amountStr)) . $amountStr;
        }

        $payload .= '5802TH';
        $payload .= '6304';

        $crc = self::crc16($payload);
        return $payload . strtoupper(sprintf('%04x', $crc));
    }

    private static function crc16(string $data): int
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
        }
        return $crc;
    }
}
