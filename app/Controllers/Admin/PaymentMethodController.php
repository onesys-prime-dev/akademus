<?php
namespace App\Controllers\Admin;

use App\Models\Payment\PaymentMethodModel;
use App\Models\Payment\PaymentMethodAccountModel;

class PaymentMethodController extends BaseAdminController {
    protected $methodModel;
    protected $accountModel;

    public function __construct() {
        $this->methodModel = new PaymentMethodModel();
        $this->accountModel = new PaymentMethodAccountModel();
    }

    public function index() {
        $methods = $this->methodModel
            ->select('payment_method.*, payment_method_account.account_number, payment_method_account.account_name')
            ->join('payment_method_account', 'payment_method_account.payment_method_id = payment_method.payment_method_id', 'left')
            ->findAll();

        return $this->renderAdmin('admin/payment_methods/index', [
            'methods' => $methods,
            'sidebarActive' => 'payment-methods'
        ]);
    }
}

?>