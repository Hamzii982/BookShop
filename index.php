<?php 

include('conn.php');

// Read JSON Files
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contents=json_decode(file_get_contents('php://input'), true);
    if(!empty($contents))
    {
        foreach ($contents as $content)
        {
            if($content['sale_id'] != null && $content['product_id'] != null)
            {
                // print_r($content);
                $sale_id = $content['sale_id'];
                $customer = $content['customer_name'];
                $mail = $content['customer_mail'];
                $product_id = $content['product_id'];
                $product_name = $content['product_name'];
                $product_price = $content['product_price'];
                $sale_date = $content['sale_date'];
                $customer = str_replace("'", "\'", $customer);
                $product_name = str_replace("'", "\'", $product_name);
                $query = "SELECT * FROM bs_products WHERE product_id='$product_id'";
                $result = $conn->query($query);
                if($result->num_rows > 0)
                {
                    echo "Product Already exists";
                }
                else{
                    $query = "INSERT INTO bs_products (product_id, product_name, product_price) VALUES ('$product_id', '$product_name', '$product_price')";
                    if($conn->query($query) === TRUE)
                    {
                        echo "Product Inserted Successfully";
                    }
                    else{
                        echo "An Error Occured".$conn->error;
                    }
                }
                $query = "SELECT * FROM bs_sales WHERE sale_id='$sale_id'";
                $result = $conn->query($query);
                if($result->num_rows > 0)
                {
                    echo "Sale Record already exists";
                }
                else{
                    $query = "INSERT INTO bs_sales (sale_id, customer_name, customer_mail, product_fid, sale_date) VALUES ('$sale_id', '$customer', '$mail', '$product_id', '$sale_date')";
                    if($conn->query($query) === TRUE)
                    {
                        echo "New Sale Inserted Successfully";
                    }
                    else{
                        echo "An Error Occured";
                    }
                }
            }
        }
    }
}

//Filter Values
if(isset($_POST['filter']))
{
    if(isset($_POST['type']) && isset($_POST['name']))
    {
        $type=$_POST['type'];
        $name=$_POST['name'];
        if($type=='customer')
        {
            $query = "SELECT * FROM bs_sales JOIN bs_products ON bs_sales.product_fid = bs_products.product_id WHERE customer_name='$name'";
            $result = $conn->query($query);
        }
        elseif($type=='book')
        {
            $query = "SELECT * FROM bs_sales JOIN bs_products ON bs_sales.product_fid = bs_products.product_id WHERE product_name='$name'";
            $result = $conn->query($query);
        }
    }
    elseif(isset($_POST['name']) || isset($_POST['type']))
    {
        echo '<script type="text/javascript">';
        echo 'setTimeout(function () { swal("Please Give Complete Info!","","info");';
        echo '}, 1000);</script>';
        header('location: index.php');
    }
    elseif(isset($_POST['max']) && isset($_POST['min']))
    {
        //Price Range in Between
        $max=$_POST['max'];
        $min=$_POST['min'];
        $query = "SELECT * FROM bs_sales JOIN bs_products ON bs_sales.product_fid = bs_products.product_id WHERE product_price<='$max' AND product_price>='$min'";
        $result = $conn->query($query);
    }
    elseif(isset($_POST['max']))
    {
        //Maximum Price Range
        $max=$_POST['max'];
        $query = "SELECT * FROM bs_sales JOIN bs_products ON bs_sales.product_fid = bs_products.product_id WHERE product_price<='$max'";
        $result = $conn->query($query);
    }
    elseif(isset($_POST['min']))
    {
        //Minimum Price Range
        $min=$_POST['min'];   
        $query = "SELECT * FROM bs_sales JOIN bs_products ON bs_sales.product_fid = bs_products.product_id WHERE product_price>='$min'";
        $result = $conn->query($query);
    }
}
else{
    //Default Table Values
    $query = "SELECT * FROM bs_sales JOIN bs_products ON bs_sales.product_fid = bs_products.product_id";

    $result = $conn->query($query);
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>My BookShop Sales</title>
        <meta charset="UTF-8">
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="path/to/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body>
        <form method='post' action="#">
            <div class="row justify-content-center m-auto mt-4" style="width: 50rem;">
                <div class="col-md-6">
                    <h6>Customer/Product filter</h6>
                    <select name="type">
                        <option value='' selected disabled>Filter type</option>
                        <option value="customer">Customer</option>
                        <option value="book">Book</option>
                    </select>
                    <input type="text" name="name" placeholder="Customer/Book Name">
                </div>
                <div class="col-md-6">
                    <h6>Price filter</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Maximum Price:</label>
                            <input type="number" name="max" style="width:50px;">
                        </div>
                        <div class="col-md-6">
                            <label>Minimum Price:</label>
                            <input type="number" name="min" style="width:50px;">
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-center mt-4">
                    <input type="submit" name="filter" class="btn btn-primary" value="Filter">
                </div>
            </div>
        </form>
        <div class="card justify-content-center m-auto mt-4" style="width: 50rem;">
            <div class="card-body">
                <h5 class="card-title text-center">My BookShop Sale Report</h5>
                <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">Sale #</th>
                            <th scope="col">Customer Name</th>
                            <th scope="col">Customer Email</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Product Price</th>
                            <th scope="col">Sale Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                if($result->num_rows > 0)
                                {
                                    $i=1;
                                    while($row = $result->fetch_assoc())
                                    {
                            ?>
                                        <tr>
                                            <th scope="row"><?=$i++?></th>
                                            <td><?=$row['customer_name']?></td>
                                            <td><?=$row['customer_mail']?></td>
                                            <td><?=$row['product_name']?></td>
                                            <td><?=$row['product_price']?></td>
                                            <td><?=$row['sale_date']?></td>
                                        </tr>
                            <?php
                                    }
                                }
                                else{
                                    echo "<tr class='text-center'><td colspan='6'>No Record Found</td></tr>";
                                }
                            ?>
                        </tbody>
                </table>
            </div>
        </div>
    </body>
</html>