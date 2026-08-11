<?php

namespace App\Controllers\Admin;

class ReportsController extends BaseAdminController
{
    public function index()
    {
        $this->guard();
        $db = \Config\Database::connect();
        $from = (string) ($this->request->getGet('from') ?: date('Y-m-01'));
        $to = (string) ($this->request->getGet('to') ?: date('Y-m-d'));

        $sales = $db->table('orders')->where('DATE(created_at) >=', $from)->where('DATE(created_at) <=', $to)->get()->getResultArray();
        $payments = $db->table('payments')->where('DATE(created_at) >=', $from)->where('DATE(created_at) <=', $to)->get()->getResultArray();
        $shipments = $db->table('shipments')->where('DATE(created_at) >=', $from)->where('DATE(created_at) <=', $to)->get()->getResultArray();

        if ($this->request->getGet('export') === 'csv') {
            $type = (string) ($this->request->getGet('type') ?: 'sales');
            return $this->exportCsv($type, compact('sales', 'payments', 'shipments'));
        }

        return view('admin/reports', [
            'title' => 'Reports',
            'from' => $from,
            'to' => $to,
            'sales' => $sales,
            'payments' => $payments,
            'shipments' => $shipments,
        ]);
    }

    private function exportCsv(string $type, array $datasets)
    {
        $map = [
            'sales' => $datasets['sales'],
            'payments' => $datasets['payments'],
            'shipments' => $datasets['shipments'],
        ];
        $rows = $map[$type] ?? $map['sales'];

        $filename = 'report-' . $type . '-' . date('YmdHis') . '.csv';
        $fh = fopen('php://temp', 'r+');

        if (!empty($rows)) {
            fputcsv($fh, array_keys($rows[0]));
            foreach ($rows as $row) {
                fputcsv($fh, $row);
            }
        } else {
            fputcsv($fh, ['message']);
            fputcsv($fh, ['No data']);
        }

        rewind($fh);
        $content = stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }
}
