<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function store() {
        $id = uniqid(true);

        // return response("Successfully updated this badge's language.", 500);

        echo json_encode([
            'data' => [
                'id' => $id
            ]
        ]);
    }

    public function upload(Request $request) {
        $path = $request->file('file')->store('/');

        return json_encode([
            'data' => [
                'success' => true,
                'path' => $path
            ]
        ]);
        //return response("Successfully updated this badge's language.", 500);

        // if (isset($_FILES['file'], $_POST['id'])) {
        //     move_uploaded_file($_FILES['file']['tmp_name'], 'C:\Users\MarshallAl\Downloads' . $_POST['id']);
        // }

    }
}
