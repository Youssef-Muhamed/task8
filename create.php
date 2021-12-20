<?php
require '../helpers/dbConnection.php';
require '../helpers/functions.php';

# Fetch Roles .....
$sql = 'select * from category';
$categories = mysqli_query($con, $sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CODE ......
    $title   = Clean($_POST['title']);
    $content = Clean($_POST['content']);
    $date    = Clean($_POST['date']);
    $cat_id  = $_POST['cat_id'];

    # Validation ......
    $errors = [];

    # Validate Name
    if (!validate($title, 1)) {
        $errors['Title'] = 'Field Required';
    } elseif (!validate($title, 7)) {
        $errors['Title'] = 'Invalid String';
    }

    # Validate Email
    if (!validate($content, 1)) {
        $errors['Content'] = 'Field Required';
    }  elseif (!validate($content, 3, 10)) {
        $errors['Content'] = 'Length Must >= 10 chs';
    }



    # Validate cat_id
    if (!validate($cat_id, 4)) {
        $errors['Category'] = 'Invalid Category';
    }

    # Validate date
    if (!validate($date, 1)) {
        $errors['date'] = 'Field Required';
    }

    // $test_date = '03/22/2010';
    $newDate  = explode('/', $date);
    if (!checkdate($newDate[0], $newDate[1], $newDate[2])) {
        $errors['Date'] = 'Invalid Date';
    }

    # Validate image
    if (!validate($_FILES['image']['name'], 1)) {
        $errors['Image'] = 'Field Required';
    } else {
        $tmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageSize = $_FILES['image']['size'];
        $imageType = $_FILES['image']['type'];

        $exArray = explode('.', $imageName);
        $extension = end($exArray);

        $FinalName = rand() . time() . '.' . $extension;

        $allowedExtension = ['png', 'jpg'];

        if (!validate($extension, 5)) {
            $errors['Image'] = 'Error In Extension';
        }
    }

    if (count($errors) > 0) {
        $_SESSION['Message'] = $errors;
    } else {
        // db ..........

        $desPath = './uploads/' . $FinalName;

        if (move_uploaded_file($tmpPath, $desPath)) {

            $date = strtotime($date);
            $sql  = "insert into blog (title,content,cat_id,image,date,added_by) values ('$title','$content',$cat_id,'$FinalName',$date,1)";
            $op = mysqli_query($con, $sql);

            if ($op) {
                $message = 'Raw Inserted';
            } else {
                $message = 'Error Try Again';
            }
        } else {
            $message = 'Error In Uploading file';
        }

        $_SESSION['Message'] = ['message' => $message];
    }
}

require '../layouts/header.php';
require '../layouts/nav.php';
require '../layouts/sidNav.php';
?>










    <div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">




            <h1 class="mt-4">Dashboard</h1>
            <ol class="breadcrumb mb-4">

                <?php

                if(isset($_SESSION['Message'])){
                    foreach($_SESSION['Message'] as $key => $val){
                        echo '* '.$key.' : '.$val.'<br>';
                    }
                    unset($_SESSION['Message']);
                }else{

                    ?>

                    <li class="breadcrumb-item active">Dashboard/Add User</li>

                <?php } ?>
            </ol>






            <div class="card-body">


                <div class="container">



                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">



                        <div class="form-group">
                            <label for="exampleInputName">Title</label>
                            <input type="text" class="form-control" id="exampleInputName" name="title"
                                   aria-describedby="" placeholder="Enter Title">
                        </div>


                        <div class="form-group">
                            <label for="exampleInputEmail">Content</label>
                            <textarea type="email" class="form-control"  name="content" placeholder="Enter email"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputPassword">date</label>
                            <input type="text" class="form-control" id="exampleInputPassword1" name="date"
                                   placeholder="date">
                        </div>





                        <div class="form-group">
                            <label for="exampleInputPassword">Category</label>

                            <select class="form-control" name="cat_id">
                                <?php
                                while($data = mysqli_fetch_assoc($categories)){
                                    ?>
                                    <option value="<?php echo $data['id']; ?>"><?php echo $data['title']; ?></option>
                                <?php } ?> ?>
                            </select>
                        </div>



                        <div class="form-group">
                            <label for="exampleInputPassword">Image</label><br>
                            <input type="file" name="image">
                        </div>


                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>

                </div>
            </div>


        </div>
    </main>


<?php

require '../layouts/footer.php';
?>