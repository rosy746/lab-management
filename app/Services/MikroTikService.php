<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class MikroTikService
{
    private string $botUrl;

    const LAB_CONFIG = [
        'lab7' => [
            'name'        => 'Lab Komputer 7',
            'nat_comment' => 'lab 7',
            'interface'   => 'lab 7',
            'dhcp_server' => 'dhcp2',
            'network'     => '192.168.70.0/24',
            'vlan_id'     => 77,
            'resource_id' => 1,
            'bot_lab_id'  => 1,
        ],
        'lab8' => [
            'name'        => 'Lab Komputer 8',
            'nat_comment' => 'lab 8',
            'interface'   => 'lab 8',
            'dhcp_server' => 'dhcp3',
            'network'     => '192.168.80.0/24',
            'vlan_id'     => 88,
            'resource_id' => 2,
            'bot_lab_id'  => 2,
        ],
    ];

    public function __construct()
    {
        $this->botUrl = config('mikrotik.bot_url', 'http://170.1.0.46:5000');
    }

    public function enableLab(string $labKey): array
    {
        $config = self::LAB_CONFIG[$labKey] ?? null;
        if (!$config) return ['success' => false, 'error' => 'Kontrol internet tidak tersedia untuk lab ini.', 'unsupported' => true];

        $response = Http::timeout(10)->post("{$this->botUrl}/api/lab/internet", [
            'lab_id' => $config['bot_lab_id'],
            'action' => 'on',
        ]);

        $data = $response->json();
        return [
            'success' => $data['success'] ?? false,
            'lab'     => $config['name'],
            'action'  => 'enabled',
            'message' => $data['message'] ?? '',
        ];
    }

    public function disableLab(string $labKey): array
    {
        $config = self::LAB_CONFIG[$labKey] ?? null;
        if (!$config) return ['success' => false, 'error' => 'Kontrol internet tidak tersedia untuk lab ini.', 'unsupported' => true];

        $response = Http::timeout(10)->post("{$this->botUrl}/api/lab/internet", [
            'lab_id' => $config['bot_lab_id'],
            'action' => 'off',
        ]);

        $data = $response->json();
        return [
            'success' => $data['success'] ?? false,
            'lab'     => $config['name'],
            'action'  => 'disabled',
            'message' => $data['message'] ?? '',
        ];
    }

    public function getLabStatus(string $labKey): array
    {
        $config = self::LAB_CONFIG[$labKey] ?? null;
        if (!$config) return ['success' => false, 'error' => 'Kontrol internet tidak tersedia untuk lab ini.', 'unsupported' => true, 'internet' => null];

        try {
            // Get status dari bot
            $statusResp = Http::timeout(10)->get("{$this->botUrl}/api/lab/status/{$config['bot_lab_id']}");
            $statusData = $statusResp->json();

            // Get devices dari bot
            $devicesResp = Http::timeout(10)->get("{$this->botUrl}/api/lab/devices/{$config['bot_lab_id']}");
            $devicesData = $devicesResp->json();

            $natEnabled  = ($statusData['status'] ?? '') === 'online';
            $devices     = $devicesData['devices'] ?? [];
            $activeUsers = count(array_filter($devices, fn($d) => $d['active'] ?? false));

            return [
                'success'      => true,
                'lab_key'      => $labKey,
                'lab_name'     => $config['name'],
                'nat_enabled'  => $natEnabled,
                'status'       => $natEnabled ? 'online' : 'offline',
                'active_users' => $activeUsers,
                'devices'      => $devices,
                'network'      => $config['network'],
            ];
        } catch (\Exception $e) {
            return [
                'success'      => false,
                'lab_key'      => $labKey,
                'lab_name'     => $config['name'],
                'nat_enabled'  => false,
                'status'       => 'error',
                'active_users' => 0,
                'devices'      => [],
                'error'        => $e->getMessage(),
            ];
        }
    }

    public function listNATRules(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->botUrl}/api/nat/rules");
            return $response->json()['rules'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function debugNATRules(): array
    {
        return $this->listNATRules();
    }
}