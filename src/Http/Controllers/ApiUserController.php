<?php

namespace Sudo\ApiUser\Http\Controllers;
use Sudo\Base\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use ListData;
use Form;
use ListCategory;

class ApiUserController extends AdminController
{
    function __construct() {
        $this->models = new \Sudo\ApiUser\Models\ApiUser;
        $this->table_name = $this->models->getTable();
        $this->module_name = 'Quản lý API Token';
        $this->has_seo = false;
        $this->has_locale = false;
        parent::__construct();

        $this->status = config('app.status');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests)
    {
        $listdata = new ListData($requests, $this->models, 'ApiUser::table.index', $this->has_locale);
        // Build Form tìm kiếm
        $listdata->search('name', 'Tên API', 'string');
        $listdata->search('status', 'Trạng thái', 'array', $this->status);
        // Build bảng
        $listdata->add('name', 'Tên API', 1);
        $listdata->add('api_token', 'Token', 0);
        $listdata->add('status', 'Tình trạng', 0, 'status');
        $listdata->add('', 'Sửa', 0, 'edit');
        $listdata->add('', 'Xóa', 0, 'delete');

        return $listdata->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Khởi tạo form
        $form = new Form;
        // Các hàm tạo Form viết ở đây
        $form->text('name', '', 1, 'Tên API');
        $form->radio('status', 1, 'Trạng thái', $this->status);
        $form->action('add');
        // Hiển thị form tại view
        return $form->render('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $requests)
    {
        // Xử lý validate
        validateForm($requests, 'name', 'Tên API không được để trống!');
        validateForm($requests, 'name', 'Tên API đã bị trùng.', 'unique', 'unique:api_users');
        // Các giá trị mặc định
        $status = 0;
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Chuẩn hóa lại dữ liệu
        $api_token = randString(60);
        // Thêm vào DB
        $created_at = $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name','api_token','status','created_at','updated_at');
        $id = $this->models->createRecord($requests, $compact, $this->has_seo, $this->has_locale);
        // Điều hướng
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $id))->with([
            'type' => 'success',
            'message' => __('Core::admin.create_success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Dẽ liệu bản ghi hiện tại
        $data_edit = $this->models->where('id', $id)->first();
        // Khởi tạo form
        $form = new Form;
        // Các hàm tạo Form viết ở đây
        $form->text('name', $data_edit->name, 1, 'Tên API');
        $form->checkbox('change_token', 0, 1, 'Đổi Token');
        $form->radio('status', $data_edit->status, 'Trạng thái', $this->status);
        $form->action('edit');
        // Hiển thị form tại view
        return $form->render('edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $requests, $id)
    {
        // Xử lý validate
        validateForm($requests, 'name', 'Tên API không được để trống.');
        validateForm($requests, 'name', 'Tên API đã bị trùng.', 'unique', 'unique:api_users,name,'.$id);
        // Lấy bản ghi
        $data_edit = $this->models->where('id', $id)->first();
        // Các giá trị mặc định
        $status = 0;
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Chuẩn hóa lại dữ liệu
        if (isset($change_token) && !empty($change_token)) {
            $api_token = randString(60);
        } else {
            $api_token = $data_edit->api_token;
        }
        // Các giá trị thay đổi
        $created_at = $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name','api_token','status','updated_at');
        // Cập nhật tại database
        $this->models->updateRecord($requests, $id, $compact, $this->has_seo);
        // Điều hướng
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $id))->with([
            'type' => 'success',
            'message' => __('Core::admin.update_success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}