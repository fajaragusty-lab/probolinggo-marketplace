<?php

namespace App\Services;

class AuditLogService
{
    public function log(string $action, ?string $entityType = null, ?int $entityId = null, array $metadata = []): void
    {
        \Config\Database::connect()->table('audit_logs')->insert([
            'user_id'     => session()->get('user_id') ?: null,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'metadata'    => empty($metadata) ? null : json_encode($metadata, JSON_UNESCAPED_UNICODE),
            'ip_address'  => service('request')->getIPAddress(),
            'user_agent'  => substr((string) service('request')->getUserAgent(), 0, 255),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
