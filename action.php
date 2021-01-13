<?php
    session_start();
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $_SESSION['user'] = $user;
        $link = __DIR__ .'/'.$_SESSION['user'].'/' ;
        // check if variable value is set or not
        if(isset($_POST["action"])){
            // return an array => list of folder names and files
            if($_POST["action"] == "fetch"){
                //$folder_file = scandir ($link . $_SESSION['user']);
                $folder_file = array_filter(glob($link.'*'),'is_dir');/*select all folders*/
                $files = array_filter(glob("$link*.*"));/* select files */
                
                // output variable => table to be displayed on screen
                $output = '
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Title/Name <i class="fas fa-chevron-down"></i></th>
                            <th scope="col">Type <i class="fas fa-chevron-down"></i></th>
                            <th scope="col">Date Added <i class="fas fa-chevron-down"></i></th>
                            <th scope="col">Manage</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                ';}
            // count number of files and folders in the table
            if(count($folder_file) > 0){
                foreach($folder_file as $name){
                        $output .= '<tr><th><i class="far fa-folder-open"></i><a style="margin-left:5px">'.basename($name).'</a></th>
                            <td>Folder</td>';
                        $output .= '<td>'. date("d M Y H:i:s", filemtime($name))  .'</td>
                            <td>
                                <ul>
                                    <li><button type="button" name="view_files" data-name="'.$name.'" class="view_files" data-toggle="modal" data-target="#filelistModal"><i class="far fa-eye" id="green"></i></button></li>
                                    <li><button type="button" name="delete" data-name="'.$name.'" class="delete"><i class="fas fa-trash-alt" id="red"></i></button></li>
                                </ul>
                            </td>
                            <td>
                                <button type="button" name="upload" data-name="'.$name.'" class="upload" id="upload_folder" data-toggle="modal" data-target="#uploadModal" style="border:transparent ; background-color:transparent;"><input type="checkbox" style="width: 20px; height: 20px;display: inline-block;
                                vertical-align: middle;" ></button>    
                            </td>
                        </tr>
                    ';}
                    foreach($files as $name){
                        $output .= '<th><a>'.basename($name).'</a></th>
                            <td>' . pathinfo($name, PATHINFO_EXTENSION) . '</td>';
                        $output .= '<td>'. date("d M Y H:i:s", filemtime($name))  .'</td>
                            <td>
                                <ul>
                                    <li><button type="button" name="open_file" data-name="'.$name.'" class="open_file"><i class="far fa-eye" id="green"></i></button></li>
                                    <li><button type="button" name="delete" data-name="'.$name.'" class="delete"><i class="fas fa-trash-alt" id="red"></i></button></li>
                                </ul>
                            </td>
                            <td>
                                <button type="button" style="border:transparent ; background-color:transparent;"><input type="checkbox" style="width: 20px; height: 20px;display: inline-block;
                                vertical-align: middle;" ></button>    
                            </td>
                        </tr>
                    ';}
                } else{
                    // $output .= '<tbody>
                    //     <tr>
                    //         <td colspan="6">No Folder Found</td>
                    //     </tr></tbody>';
                    }
                //$output .= '</table>';
                echo $output;
            }
            // create folders
            if($_POST["action"] == "create"){
                if(!file_exists($_POST["folder_name"])) 
                {
                    mkdir($_POST["folder_name"], 0777, true);
                    echo 'Folder Created';
                    }
                else{
                    echo 'Folder Already Created';
                    }
                }
            // delete folder
            if($_POST["action"] == "delete"){
                $files = scandir($_POST["folder_name"]);
                if(file_exists($_POST["folder_name"]))
                {
                    unlink($_POST["folder_name"]);
                }
                foreach($files as $file){
                    if($file === '.' or $file === '..'){
                        continue;
                    } else{
                        unlink($_POST["folder_name"] . '/' . $file);
                    }
                }
                    if(rmdir($_POST["folder_name"]))
                    {
                        echo 'Deleted';
                    }
            }

            // view folder
            if($_POST["action"] == "fetch_files"){
                $file_data = scandir($_POST["folder_name"]);
                $output = '
                <table class="table table-bordered table-striped">
                <tr>
                    <th>File</th>
                    <th>File Name</th>
                    <th>Delete</th>
                </tr>';
            
                foreach($file_data as $file)
                {
                    if($file === '.' or $file === '..')
                    {
                        continue;
                    }
                    else
                    {
                        $path = $_POST["folder_name"] . '/' . $file;
                        $output .= '
                        <tr>
                        <td><img src="'.$path.'" class="img-thumbnail" height="50" width="50" /></td>
                        <td contenteditable="true" data-folder_name="'.$_POST["folder_name"].'"  data-file_name = "'.$file.'" class="change_file_name">'.$file.'</td>
                        <td><button name="remove_file" class="remove_file btn btn-danger btn-xs" id="'.$path.'">Remove</button></td>
                        </tr>';
                    }
                }
                $output .='</table>';
                echo $output;
            }
            // remove files from folder
            if($_POST["action"] == "remove_file"){
                if(file_exists($_POST["path"]))
                {
                    unlink($_POST["path"]);
                    echo 'Deleted';
                }
                $files = scandir($_POST["path"]);
                foreach($files as $file){
                    if($file === '.' or $file === '..'){
                        continue;
                    } else{
                        unlink($_POST["path"] . '/' . $file);
                    }
                }
                if(rmdir($_POST["path"])){
                    // echo 'Folder Deleted';
                }
            }

            // view files

            if($_POST["action"] == "open_files"){
                $file_to_view = $_POST['folder_name']; 
                header('Content-type: application/pdf');
                header('Content-Type: text/css');
                header('Content-Type: image/png');
                header('Content-Type: image/jpg');
                header('Content-Type: image/jpeg');
                header('Content-Type: image/svg');
                header('Content-Type: application/json');
                header('Content-Type: text/plain');
                header('Content-Disposition: inline; filename="' . $_POST['folder_name'] . '"');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: ' . filesize($file_to_view));
                @readfile($file_to_view);
            }
    }
    
    