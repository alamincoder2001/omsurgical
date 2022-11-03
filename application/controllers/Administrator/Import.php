<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Import extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->cbrunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->model('SMS_model', 'sms', true);
        $this->load->model('import_model');
    }

    public function index()
    {
        $query =  $this->db->query('SELECT * from register ORDER BY id desc');
        $records = $query->result_array();
        $data['user_data'] = $records;
        $this->load->view('excel_file_upload', $data);
    }

    public function uploadData()
    {
        echo 'ok';
        exit;

        if ($_FILES["import_excel"]["name"] != '') {
            $allowed_extension = array('xls', 'csv', 'xlsx');
            $file_array = explode(".", $_FILES["import_excel"]["name"]);
            $file_extension = end($file_array);

            if (in_array($file_extension, $allowed_extension)) {
                $file_name = time() . '.' . $file_extension;
                move_uploaded_file($_FILES['import_excel']['tmp_name'], $file_name);
                $file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file_name);
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file_type);

                $spreadsheet = $reader->load($file_name);

                unlink($file_name);

                $data = $spreadsheet->getActiveSheet()->toArray();

                foreach ($data as $row) {
                    $insert_data = array(
                        ':first_name'  => $row[0],
                        ':last_name'  => $row[1],
                        ':created_at'  => $row[2],
                        ':updated_at'  => $row[3]
                    );

                    $query = "
           INSERT INTO sample_datas 
           (first_name, last_name, created_at, updated_at) 
           VALUES (:first_name, :last_name, :created_at, :updated_at)
           ";

                    $statement = $connect->prepare($query);
                    $statement->execute($insert_data);
                }
                $message = '<div class="alert alert-success">Data Imported Successfully</div>';
            } else {
                $message = '<div class="alert alert-danger">Only .xls .csv or .xlsx file allowed</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Please Select File</div>';
        }

        echo $message;
    }
}
