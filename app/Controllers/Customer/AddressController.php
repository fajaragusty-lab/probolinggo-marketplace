<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class AddressController extends BaseController
{
    public function index()
    {
        $addresses = \Config\Database::connect()->table('addresses')
            ->where('user_id', (int) session()->get('user_id'))
            ->orderBy('is_default', 'DESC')->get()->getResultArray();
        return view('customer/addresses/index', ['addresses' => $addresses]);
    }

    public function create()
    {
        return view('customer/addresses/form', ['address' => null]);
    }

    public function store()
    {
        $userId = (int) session()->get('user_id');
        $rules = [
            'label' => 'required|max_length[50]',
            'recipient_name' => 'required|max_length[150]',
            'phone' => 'required|min_length[10]',
            'address' => 'required',
            'district' => 'required',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $db = \Config\Database::connect();
        $isDefault = $this->request->getPost('is_default') ? 1 : 0;
        if ($isDefault) {
            $db->table('addresses')->where('user_id', $userId)->update(['is_default' => 0]);
        }
        $db->table('addresses')->insert([
            'user_id' => $userId,
            'label' => $this->request->getPost('label'),
            'recipient_name' => $this->request->getPost('recipient_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'district' => $this->request->getPost('district'),
            'city' => $this->request->getPost('city') ?: 'Probolinggo',
            'postal_code' => $this->request->getPost('postal_code'),
            'latitude' => $this->normalizeCoordinate($this->request->getPost('latitude')),
            'longitude' => $this->normalizeCoordinate($this->request->getPost('longitude')),
            'location_recorded_at' => $this->normalizeRecordedAt((string) $this->request->getPost('location_recorded_at')),
            'is_default' => $isDefault,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->to('/addresses')->with('success', 'Alamat ditambahkan');
    }

    public function edit($id)
    {
        $address = \Config\Database::connect()->table('addresses')
            ->where(['id' => $id, 'user_id' => (int) session()->get('user_id')])->get()->getRowArray();
        if (!$address) {
            return redirect()->to('/addresses')->with('error', 'Alamat tidak ditemukan');
        }
        return view('customer/addresses/form', ['address' => $address]);
    }

    public function update($id)
    {
        $userId = (int) session()->get('user_id');
        $db = \Config\Database::connect();
        $address = $db->table('addresses')->where(['id' => $id, 'user_id' => $userId])->get()->getRowArray();
        if (!$address) {
            return redirect()->to('/addresses')->with('error', 'Alamat tidak ditemukan');
        }
        $isDefault = $this->request->getPost('is_default') ? 1 : 0;
        if ($isDefault) {
            $db->table('addresses')->where('user_id', $userId)->update(['is_default' => 0]);
        }
        $db->table('addresses')->where('id', $id)->update([
            'label' => $this->request->getPost('label'),
            'recipient_name' => $this->request->getPost('recipient_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'district' => $this->request->getPost('district'),
            'city' => $this->request->getPost('city') ?: 'Probolinggo',
            'postal_code' => $this->request->getPost('postal_code'),
            'latitude' => $this->normalizeCoordinate($this->request->getPost('latitude')),
            'longitude' => $this->normalizeCoordinate($this->request->getPost('longitude')),
            'location_recorded_at' => $this->normalizeRecordedAt((string) $this->request->getPost('location_recorded_at')),
            'is_default' => $isDefault,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->to('/addresses')->with('success', 'Alamat diperbarui');
    }

    public function delete($id)
    {
        \Config\Database::connect()->table('addresses')
            ->where(['id' => $id, 'user_id' => (int) session()->get('user_id')])->delete();
        return redirect()->to('/addresses')->with('success', 'Alamat dihapus');
    }

    private function normalizeCoordinate($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 7);
    }

    private function normalizeRecordedAt(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        try {
            return (new \DateTime($value))->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }
}
