<?php
session_start();
if(!isset($_SESSION['name'])) {
  header("Location:../index.php");
}
$level=$_SESSION['level'];
$name=$_SESSION['name'];
$costcenter=$_SESSION['costcenter'];
include ("../process/dbconn.php");
$conn=new dbconn();
$so_tb=$conn->getOne("SELECT COUNT(id) AS dem FROM thongbao");
$_SESSION["so_tb"] = $so_tb['dem'];
$ds_tb=$conn->getAll("SELECT * FROM thongbao");
$_SESSION["ds_tb"] = $ds_tb;

$case = isset($_GET['case']) ? $_GET['case'] : 'check';
// echo $case;
if($case === "incomplete"){

  if(isset($_GET['build'])){

    $location = $_GET['build'];
    $ds_costcenter = $conn->getOne("SELECT GROUP_CONCAT(costcenter) AS costcenters FROM nhom WHERE location =  '$location'");
    if($ds_costcenter){
      $costcenters = $ds_costcenter['costcenters'];
      $costcenterArray = explode(',', $costcenters);
      
      // Quote each value and join them back into a string
      $quotedCostcenters = implode("','", $costcenterArray);
      $ds_hien = $conn->getAll("SELECT * FROM wh_data WHERE costcenter IN('$quotedCostcenters') AND (LEFT(modified_date, 7) < DATE_FORMAT(CURDATE(), '%Y-%m') OR modified_date IS NULL) AND keydata IS NOT NULL GROUP BY keydata");

    }
    
  }
}
// else if ($case ==="check") {
//   // code..
//   echo "cose";
// }{}



// if (isset($_GET['key'])){
//   $key=$_GET['key'];
//   $ds_vattu=$conn->getAll("SELECT * FROM wh_data WHERE product_id LIKE '%$key%' LIMIT 0,10");
// }else{
//   $ds_vattu=$conn->getAll("SELECT * FROM wh_data WHERE qty < 0 LIMIT 0,10");
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Tool Management</title>

  <!-- Bootstrap -->
  <link href="../resource/css/bootstrap.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="../resource/css/font-awesome.css" rel="stylesheet">
  <!-- NProgress -->
  <link href="../resource/css/nprogress.css" rel="stylesheet">
  <!-- iCheck -->
  <link href="../resource/css/green.css" rel="stylesheet">
  <!-- Datatables -->
  <link href="../resource/css/dataTables.bootstrap.css" rel="stylesheet">
  <link href="../resource/css/buttons.bootstrap.css" rel="stylesheet">
  <link href="../resource/css/fixedHeader.bootstrap.css" rel="stylesheet">
  <link href="../resource/css/responsive.bootstrap.css" rel="stylesheet">
  <link href="../resource/css/scroller.bootstrap.css" rel="stylesheet">
  <!-- Custom Theme Style -->
  <link href="../resource/css/custom.css" rel="stylesheet">
  <!-- SMC Icon -->
  <link rel="shortcut icon" href="../resource/images/smc.ico" />
  
  <link rel="stylesheet" type="text/css" href="../resource/fontawesome/css/all.min.css">

  <!-- <link rel="stylesheet" type="text/css" href="../resource/bootstrap/css/bootstrap.min.css"> -->
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function() {
    // Gán giá trị PHP vào biến case_type
    var case_type = "<?= $case; ?>";

    // Kiểm tra giá trị case_type
    if (case_type === "check") {
      // Tìm thẻ table bằng class và xóa thuộc tính id
      var table = document.querySelector('.table.table-striped.table-bordered');
      if (table) {
        table.removeAttribute('id');
      }
    }
  });
</script>


</head>

<body class="nav-md">
<div class="container body">
  <div class="main_container">

    <!-- side and top bar include -->
    <?php include 'top_side.php' ?>
    <!-- /side and top bar include -->

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="clearfix"></div>
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Monthly checksheet <small></small></h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                      <ul class="dropdown-menu" role="menu">
                        <!-- <li><a href="dashboard.php">All</a>
                        </li>
                        <li><a href="dashboard.php?type=CCDC">CCDC</a>
                        </li>
                        <li><a href="dashboard.php?type=VTDG">VTDG</a>
                        </li> -->
                      </ul>
                    </li>
                    <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <p class="red">Let's scan location code to checksheet</p>
                <!-- Ngày tạo -->
                <div class="form-horizontal form-label-left" >
                <div class="item form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Location <span class="required">*</span></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="location" class="form-control col-md-7 col-xs-12" placeholder="Scan location code" required type="text">
                  </div>
                </div>
                </div>
                <div class="ln_solid"></div>
                <div class="text-center"><h3 id="info_location"></h3></div>
                <div class="item form-group" style="display: flex; align-items: center;">
                  <label class="control-label" style="margin-right: 20px;">Item <span class="required">*</span></label>
                  <div style="">
                    <input id="item_id" class="form-control" placeholder="Scan item code" required type="text" style="width: 200px;">
                  </div>
                  <div style="flex-grow: 1;"><p class="red" id="notify-item" style="margin-left: 20px;"></p></div>
                </div>



                <div class="clearfix"></div>
                <p class="text-muted font-13 m-b-30"></p>
                <table id="datatable-buttons" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Product</th>
                      <th>Product name</th>
                      <th>Status</th>
                      <th>Costcenter</th>
                      <th>Type</th>
                      <th style="width: 18%;">Last check</th>
                      <th style="width: 10%;">Check</th>
                      <th style="width: 8%;">Command</th>
                    </tr>
                  </thead>
                  <tbody id="data_checksheet">
                <?php
                if(isset($ds_hien)){
                  $stt = 0;
                  foreach($ds_hien as $ds){
                    if($ds['modified_date'] == NULL || $ds['modified_by'] == NULL){
                      $last_check = "Not yet";
                    }else{
                      $last_check = substr($ds['modified_date'], 0, 10)." | ". $ds['modified_by'];
                    }
                    echo "<tr>";
                    echo "<td>".$stt++."</td>";
                    echo "<td>".$ds['product_id']."</td>";
                    echo "<td>".$ds['product_name']."</td>";
                    echo "<td>".$ds['status']."</td>";
                    echo "<td>".$ds['costcenter']."</td>";
                    echo "<td>".$ds['type']."</td>";
                    
                    echo "<td>".$last_check."</td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "</tr>";
                  }
                }
                ?>
                  </tbody>

                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- <div id ='result'></div> -->
    <!-- /page content -->

    <!-- footer content include -->
    <?php include 'footer.php' ?>
    <!-- /footer content include -->
  </div>

</div>

<!-- jQuery -->
<script src="../resource/js/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../resource/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="../resource/js/fastclick.js"></script>
<!-- NProgress -->
<script src="../resource/js/nprogress.js"></script>
<!-- iCheck -->
<script src="../resource/js/icheck.min.js"></script>
<!-- Datatables -->
<script src="../resource/js/jquery.dataTables.min.js"></script>
<script src="../resource/js/dataTables.bootstrap.min.js"></script>
<script src="../resource/js/dataTables.buttons.min.js"></script>
<script src="../resource/js/buttons.bootstrap.min.js"></script>
<script src="../resource/js/buttons.flash.min.js"></script>
<script src="../resource/js/buttons.html5.min.js"></script>
<script src="../resource/js/buttons.print.min.js"></script>
<script src="../resource/js/dataTables.fixedHeader.min.js"></script>
<script src="../resource/js/dataTables.keyTable.min.js"></script>
<script src="../resource/js/dataTables.responsive.min.js"></script>
<script src="../resource/js/responsive.bootstrap.js"></script>
<script src="../resource/js/dataTables.scroller.min.js"></script>
<script src="../resource/js/jszip.min.js"></script>
<script src="../resource/js/pdfmake.min.js"></script>
<script src="../resource/js/vfs_fonts.js"></script>
<!-- Custom Theme Scripts -->
<script src="../resource/js/custom.min.js"></script>
<script type="text/javascript">
  $(document).ready(function(){

    var location;
    var checkPosition =  false;
    var item_id;
    var case_type = "<?=$case;?>";

    $('#location').on('keydown', function (e) {
      if (e.key === "Enter" || e.key ==="Tab") {
        if ($('#location').val().trim() !== '') {
          console.log('Ô input có dữ liệu:', $('#location').val());
          location = $('#location').val().trim();
      
          fetch('../process/data_checksheet.php', {
            method: 'POST',  // Phương thức gửi dữ liệu
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'location=' + encodeURIComponent(location),
          })
          .then(response => response.text())
          .then(data => {
            document.getElementById('data_checksheet').innerHTML = data;
            $('#location').val("");
            if($(".not-found").length ==0){
              $('#info_location').html(location);
              $('#item_id').focus();
            }
            
            
            checkPosition = true;
          })
          .catch(error => {
            console.error('Có lỗi xảy ra:', error);
            checkPosition =  false;
          });
        } else {
          console.log('Ô input đang trống');
        }
      }

      


    });

    $('#item_id').on('keydown', function (e) {
      if (e.key === "Enter" || e.key ==="Tab") {

        if (!checkPosition) {
            $("#notify-item").html("*You have not scanned the location code yet");
        } else {
            $("#notify-item").html("");
            const item_id = $('#item_id').val().trim();  // Lấy giá trị từ input và loại bỏ khoảng trắng đầu cuối
            const targetRow = $(`tr[data-id='${item_id}']`);  // Tìm dòng có data-id khớp với item_id
            
            if (targetRow.length > 0) {
                $("#notify-item").html("");

                const checkCell = targetRow.find(".check");
                const checkdateCell = targetRow.find(".datacheck");
                const productCell = targetRow.find(".product");
                const product = productCell.html();
                const costcenterCell = targetRow.find(".costcenter");
                const costcenter = costcenterCell.html();
                

                fetch('../process/update_checksheet.php', {
                  method: 'POST',  // Phương thức gửi dữ liệu
                  headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                  },
                  body: 'location=' + encodeURIComponent(location) + 
                        '&item_id=' + encodeURIComponent(item_id) +
                        '&product=' + encodeURIComponent(product) +
                        '&costcenter=' + encodeURIComponent(costcenter)
                })
                .then(response => response.text())
                .then(data => {
                  console.log(data);
                  if(data ==="Update done"){
                    checkCell.css("background", 'green');  // Đổi màu nền thành xanh
                    checkCell.html("<i class='fa-solid fa-check' style='color: white; font-size: 16px;'></i>");
                    
                    checkdateCell.html("<?=date('Y-m-d').' | '.$name; ?>");  // Cập nhật ngày và tên vào ô checkdate
                    $('#item_id').val("");  // Xóa giá trị của input có id "item_id"
                  }
                })
                .catch(error => {
                  console.error('Có lỗi xảy ra:', error);
                });

            } else {
                $("#notify-item").html("*No product found");  // Nếu không tìm thấy sản phẩm
            }
        }

      }

    });
  })
</script>
<script type="text/javascript" src="../resource/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#modalStatus').on('show.bs.modal', function (event) {
        // $('#modalStatus').removeAtrr("aria-hidden");

        // Lấy phần tử kích hoạt modal
        var triggerElement = event.relatedTarget;
        // Lấy dữ liệu từ thuộc tính `data-*`
        var id = $(triggerElement).data('id');
        var location = $(triggerElement).data('location');
        var product = $(triggerElement).data('product');
        var productname = $(triggerElement).data('productname');
        // var unit = $(triggerElement).data('unit');
        var costcenter = $(triggerElement).data('costcenter');
        var type = $(triggerElement).data('type');
        var status = $(triggerElement).data('status');

        $("#id-up").val(id);
        $("#location-up").val(location);
        $("#product-up").val(product);
        $("#productname-up").val(productname);
        // $("#unit-up").val(unit);
        $("#costcenter-up").val(costcenter);
        $("#type-up").val(type);
        $("#status-up").val(status);
    });
    $("#btnUpdateStatus").click(()=>{

      var status = $("#status-up").val().trim();
      var id = $("#id-up").val().trim();
      // alert(id);
      var location = $("#location-up").val().trim();
      var product = $("#product-up").val().trim();
      var costcenter = $("#costcenter-up").val().trim();

      fetch('../process/update_checksheet_status.php', {
        method: 'POST',  // Phương thức gửi dữ liệu
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'location=' + encodeURIComponent(location) + 
              '&id=' + encodeURIComponent(id) +
              '&status=' + encodeURIComponent(status) +
              '&product=' + encodeURIComponent(product) +
               '&costcenter=' + encodeURIComponent(costcenter)
      })
      .then(response => response.text())
      .then(data => {
        console.log(data);
        if(data ==="Update successfully"){

          const targetRow = $(`tr[data-id='${id}']`);
          const statusCell = targetRow.find(".status");
          statusCell.html(status);
          var modalEmelent = $('#modalStatus');
          var modalInstance = modalEmelent.data('bs.modal');
          if (modalInstance) {
            modalInstance.hide(); // Gọi phương thức hide từ instance modal
          }
        }
      })
      .catch(error => {
        console.error('Có lỗi xảy ra:', error);
      });
      

    })
});


</script>
<div class="modal fade" id="modalStatus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
          <div class="mb-3" hidden>
              <label for="id-up" class="form-label bolt-1">ID</label>
              <input type="text" class="form-control" id="id-up" name="id-up">
          </div>
          <div class="mb-3">
              <label for="location-up" class="form-label bolt-1">Location</label>
              <input type="text" class="form-control" id="location-up" name="location-up" readonly>
          </div>
          <div class="mb-3">
              <label for="product-up" class="form-label bolt-1">Product</label>
              <input type="text" class="form-control" id="product-up" name="product-up" readonly>
          </div>
          <div class="mb-3">
              <label for="productname-up" class="form-label bolt-1">Product name</label>
              <input type="text" class="form-control" id="productname-up" name="productname-up" readonly>
          </div>
          <!-- <div class="mb-3">
              <label for="unit-up" class="form-label bolt-1">Unit</label>
              <input type="text" class="form-control" id="unit-up" name="unit-up" readonly>
          </div> -->
          <div class="mb-3">
              <label for="costcenter-up" class="form-label bolt-1">Quanity</label>
              <input type="text" class="form-control" id="costcenter-up" name="qty-up" readonly>
          </div>
          <div class="mb-3">
              <label for="type-up" class="form-label bolt-1">Type</label>
              <input type="text" class="form-control" id="type-up" name="type-up" readonly>
          </div>
          <div class="mb-3">
            <div class="form-group">
              <label for="status-up">Chọn một tùy chọn:</label>
              <select class="form-control" id="status-up" name="status-up" required>
                  <option value="LOST">LOST</option>
                  <option value="DAMAGE">DAMAGE</option>
                  <option value="OK">OK</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btnUpdateStatus" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>
