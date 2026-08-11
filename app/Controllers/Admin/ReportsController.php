<?php

namespace App\Controllers\Admin;

class ReportsController extends BaseAdminController
{
    public function index()
    {
        $guard = $this->guard();
        if ($guard) {
            return $guard;
        }
        $db = \Config\Database::connect();
        $fromInput = (string) ($this->request->getGet('from') ?: date('Y-m-01'));
        $toInput = (string) ($this->request->getGet('to') ?: date('Y-m-d'));
        $from = $this->normalizeDate($fromInput, date('Y-m-01'));
        $to = $this->normalizeDate($toInput, date('Y-m-d'));

        if ($this->request->getGet('export') === 'csv') {
            $type = (string) ($this->request->getGet('type') ?: 'sales');
            return $this->exportCsv($type, $from, $to);
        }

        $salesCount = $db->table('orders')->where('DATE(created_at) >=', $from)->where('DATE(created_at) <=', $to)->countAllResults();
        $paymentsCount = $db->table('payments')->where('DATE(created_at) >=', $from)->where('DATE(created_at) <=', $to)->countAllResults();
        $shipmentsCount = $db->table('shipments')->where('DATE(created_at) >=', $from)->where('DATE(created_at) <=', $to)->countAllResults();

        return view('admin/reports', [
            'title' => 'Reports',
            'from' => $from,
            'to' => $to,
            'salesCount' => $salesCount,
            'paymentsCount' => $paymentsCount,
            'shipmentsCount' => $shipmentsCount,
        ]);
    }

    private function exportCsv(string $type, string $from, string $to)
    {
        $db = \Config\Database::connect();
        $resolvedType = in_array($type, ['sales', 'payments', 'shipments'], true) ? $type : 'sales';
        $table = $resolvedType === 'sales' ? 'orders' : ($resolvedType === 'payments' ? 'payments' : 'shipments');
        $builder = $db->table($table)
            ->where('DATE(created_at) >=', $from)
            ->where('DATE(created_at) <=', $to);
        $rows = $builder->get()->getResultArray();

        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', 'report-' . $resolvedType . '-' . date('YmdHis') . '.csv');
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

    private function normalizeDate(string $value, string $fallback): string
    {
        $dt = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$dt || $dt->format('Y-m-d') !== $value) {
            return $fallback;
        }

        return $value;
    }
}
