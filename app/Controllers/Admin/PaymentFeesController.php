<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ThemeModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentMethodModel;
use App\Models\Payment\PaymentMethodAccountModel;

class PaymentFeesController extends BaseController
{
    private function requireAdmin()
    {
        if (! session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }
        return null;
    }

    // ── Fee Settings ──────────────────────────────────────────────

    public function fees()
    {
        if ($r = $this->requireAdmin()) return $r;

        $feeModel    = new PaymentFeeTypeModel();
        $methodModel = new PaymentMethodModel();
        $theme       = (new ThemeModel())->getAllSettings();

        // Payment methods joined with their account (1-to-1)
        $db      = \Config\Database::connect();
        $methods = $db->table('payment_method pm')
            ->select([
                'pm.payment_method_id',
                'pm.code',
                'pm.name',
                'pm.description',
                'pm.is_active',
                'pma.payment_method_account_id',
                'pma.account_number',
                'pma.account_name',
            ])
            ->join('payment_method_account pma', 'pma.payment_method_id = pm.payment_method_id', 'left')
            ->orderBy('pm.payment_method_id', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/payments/fees', [
            'theme'   => $theme,
            'fees'    => $feeModel->findAll(),
            'methods' => $methods,
        ]);
    }

    public function updateFee($id)
    {
        if ($r = $this->requireAdmin()) return $r;

        $amount = $this->request->getPost('amount');

        if ($amount === null || ! is_numeric($amount) || (float)$amount < 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid amount.']);
        }

        (new PaymentFeeTypeModel())->update((int)$id, ['amount' => (float)$amount]);

        return $this->response->setJSON(['success' => true, 'message' => 'Fee amount updated.']);
    }

    // ── Payment Method CRUD ───────────────────────────────────────

    public function addMethod()
    {
        if ($r = $this->requireAdmin()) return $r;

        $name          = trim($this->request->getPost('name') ?? '');
        $accountNumber = trim($this->request->getPost('account_number') ?? '');
        $accountName   = trim($this->request->getPost('account_name') ?? '');

        if ($name === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Method name is required.']);
        }

        // Generate a code from the name (uppercase, spaces → underscores, keep alphanumeric + _)
        $code = strtoupper(preg_replace('/[^A-Z0-9_]/i', '', str_replace(' ', '_', $name)));

        $db = \Config\Database::connect();
        $db->transStart();

        $methodModel = new PaymentMethodModel();
        $methodId    = $methodModel->insert([
            'code'      => $code,
            'name'      => $name,
            'is_active' => 1,
        ], true);

        if ($methodId && ($accountNumber !== '' || $accountName !== '')) {
            (new PaymentMethodAccountModel())->insert([
                'payment_method_id' => $methodId,
                'account_number'    => $accountNumber,
                'account_name'      => $accountName,
                'is_active'         => 1,
            ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Database error. Please try again.']);
        }

        // Return the new row data for the JS to render
        $row = $db->table('payment_method pm')
            ->select([
                'pm.payment_method_id',
                'pm.code',
                'pm.name',
                'pm.description',
                'pm.is_active',
                'pma.payment_method_account_id',
                'pma.account_number',
                'pma.account_name',
            ])
            ->join('payment_method_account pma', 'pma.payment_method_id = pm.payment_method_id', 'left')
            ->where('pm.payment_method_id', $methodId)
            ->get()
            ->getRowArray();

        return $this->response->setJSON(['success' => true, 'method' => $row]);
    }

    public function updateMethod(int $id)
    {
        if ($r = $this->requireAdmin()) return $r;

        $name          = trim($this->request->getPost('name') ?? '');
        $accountNumber = trim($this->request->getPost('account_number') ?? '');
        $accountName   = trim($this->request->getPost('account_name') ?? '');
        $accountId     = (int) ($this->request->getPost('account_id') ?? 0);

        if ($name === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Method name is required.']);
        }

        $methodModel  = new PaymentMethodModel();
        $accountModel = new PaymentMethodAccountModel();

        $methodModel->update($id, ['name' => $name]);

        if ($accountId) {
            $accountModel->update($accountId, [
                'account_number' => $accountNumber,
                'account_name'   => $accountName,
            ]);
        } else {
            // No account row yet — insert one
            $accountModel->insert([
                'payment_method_id' => $id,
                'account_number'    => $accountNumber,
                'account_name'      => $accountName,
                'is_active'         => 1,
            ]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function toggleMethod(int $id)
    {
        if ($r = $this->requireAdmin()) return $r;

        $methodModel = new PaymentMethodModel();
        $method      = $methodModel->find($id);

        if (! $method) {
            return $this->response->setJSON(['success' => false, 'message' => 'Method not found.']);
        }

        $newState = $method['is_active'] ? 0 : 1;
        $methodModel->update($id, ['is_active' => $newState]);

        return $this->response->setJSON(['success' => true, 'is_active' => $newState]);
    }
}
