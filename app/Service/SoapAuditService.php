<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class SoapAuditService
{

    public function sendAuditLog(string $teamId, string $activityName, array $logData, string $token): string
    {
        $url = rtrim(config('services.iae_sso.url'), '/') . '/soap/v1/audit';
        $jsonData = json_encode($logData);

        $xmlBody = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:iae="http://iae.central/audit">
    <soap:Body>
        <iae:AuditRequest>
            <iae:TeamID>' . htmlspecialchars($teamId) . '</iae:TeamID>
            <iae:ActivityName>' . htmlspecialchars($activityName) . '</iae:ActivityName>
            <iae:LogContent><![CDATA[' . $jsonData . ']]></iae:LogContent>
        </iae:AuditRequest>
    </soap:Body>
</soap:Envelope>';

        $response = Http::withHeaders([
            'Content-Type' => 'text/xml',
        ])->withToken($token)->send('POST', $url, [
            'body' => $xmlBody
        ]);

        return $this->parseXmlResponse($response->body());
    }

    private function parseXmlResponse(string $xmlContent): string
    {
        if (preg_match('/<iae:ReceiptNumber>(.*?)<\/iae:ReceiptNumber>/is', $xmlContent, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/<ReceiptNumber>(.*?)<\/ReceiptNumber>/is', $xmlContent, $matches)) {
            return trim($matches[1]);
        }

        return "IAE-LOG-LOCAL-" . strtoupper(uniqid());
    }
}