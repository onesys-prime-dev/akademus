<?php

namespace App\Controllers\Admin;

use App\Models\ThemeModel;
use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentMethodModel;
use App\Models\Payment\PaymentMethodAccountModel;


class PaymentFeesController extends BaseAdminController
{
    public function index()
    {
        $fees = $this->paymentFeeModel->findAll();
        return view('admin/payment_fees', ['fees' => $fees]);
    }

    public function update()
    {
        $data = $this->request->getPost();
        foreach ($data as $id => $fee) {
            if (is_numeric($id)) {
                $this->paymentFeeModel->update($id, ['amount' => floatval($fee)]);
            }
        }
        return redirect()->back()->with('success', 'Payment fees updated successfully.');
    }
    
}

