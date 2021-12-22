<!DOCTYPE html>
<html>
  <head>
    <title>Customer</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" type="text/css" href="../CSS/style.css"/>
    <style>
    html,body,h1,h2,h3,h4,h5 {font-family: "Consolas", sans-serif}
    </style>
  </head>
  <?php
    ini_set('display_errors', 1);
    require_once "../config.php";

    require_once DB_CONNECT;
    require_once DATA_CUSTOMER;
    include_once VIEW_HEADER;


    $current_scheme = $product_scheme;

    $selection = $current_scheme->GetPostValues(true, "%");

    $modifying = TryGetValue("modifying", null);
    $modifying = $modifying != null;


    $offset = TryGetValue("offset", "0");

    $results = $current_scheme->Select($conn, "*", $selection, offset: $offset);
  ?>
  <body class="w3-light-grey">

  <?php include "top_bar.php" ?>

  <!-- Sidebar/menu -->
  <nav class="w3-sidebar w3-collapse w3-white" style="z-index:3;width:300px;" id="mySidebar">
    <a href="https://r-dap.com"><img style="width: 300px; object-fit: cover;" src="banner.jpg"></a>
    <div class="w3-container">
      <h5>資料庫</h5>
    </div>
    <div class="w3-bar-block">
      <a href="new_customer.php" class="w3-bar-item w3-button w3-padding"><i class="fa material-icons fa-fw">people</i>  客戶基本資料</a>
      <a href="new_order.php" class="w3-bar-item w3-button w3-padding"><i class="fa material-icons fa-fw">pending_actions</i>  客戶訂貨記錄</a>
      <a href="new_product.php" class="w3-bar-item w3-button w3-padding w3-blue"><i class="fa material-icons fa-fw">widgets</i>  公司進貨</a>
      <a href="new_receivable.php" class="w3-bar-item w3-button w3-padding"><i class="fa material-icons fa-fw">account_balance_wallet</i>  公司應收帳款</a>
    </div>
  </nav>


  <!-- Overlay effect when opening sidebar on small screens -->
  <div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

  <!-- !PAGE CONTENT! -->
  <div class="w3-main" style="margin-left:300px;margin-top:43px;">

    <!-- Dash board -->
    <header class="w3-container" style="padding-top:22px">
      <h5><b><i class="fa fa-dashboard"></i> 資料統計</b></h5>
    </header>

    <div class="w3-row-padding w3-margin-bottom">
      <div class="w3-quarter">
        <div class="w3-container w3-red w3-padding-16">
          <div class="w3-left"><i class="fa material-icons w3-xxxlarge">info</i></div>
          <div class="w3-right">
            <h3>
              <?php
                echo $current_scheme->CountRows($conn, 'supplier_id');
              ?>
            </h3>
          </div>
          <div class="w3-clear"></div>
          <h4>總資料數量</h4>
          
        </div>
      </div>
      <div class="w3-quarter">
        <div class="w3-container w3-blue w3-padding-16">
          <div class="w3-left"><i class="fa material-icons w3-xxxlarge">contact_page</i></div>
          <div class="w3-right">
            <h3>
              <?php
                echo $current_scheme->CountRows($conn, 'product_count');
              ?>
            </h3>
          </div>
          <div class="w3-clear"></div>
          <h4>總進貨數量</h4>
        </div>
      </div>
      <div class="w3-quarter">
        <div class="w3-container w3-teal w3-padding-16">
          <div class="w3-left"><i class="fa fa-share-alt w3-xxxlarge"></i></div>
          <div class="w3-right">
            <h3>
              <?php
                echo $current_scheme->MaxRows($conn, 'product_value_per_unit');
              ?>
            </h3>
          </div>
          <div class="w3-clear"></div>
          <h4>最高進貨單價</h4>
        </div>
      </div>
      <div class="w3-quarter">
        <div class="w3-container w3-orange w3-text-white w3-padding-16">
          <div class="w3-left"><i class="fa fa-users w3-xxxlarge"></i></div>
          <div class="w3-right">
            <h3>
              <?php
                echo $current_scheme->SumRows($conn, 'total_value');
              ?>
            </h3>
          </div>
          <div class="w3-clear"></div>
          <h4>總價值</h4>
        </div>
      </div>
    </div>

    <!-- End Dash board -->

    <!-- Regions -->

    <div class="w3-medium" style="overflow: scroll; white-space: nowrap">
      <div class="w3-container">
        <h5>資料表</h5>
      </div>
      <table class="w3-table w3-striped w3-white" >
        <form method="post" action="" class="form-container">
          <tr>
            <td></td>
            <?php echo create_headers($current_scheme); ?>
          </tr>
          <tr>
            <td><input type="submit" style='width:50px;'></td>
            <?php echo create_inputs($conn, $current_scheme, use_default: "post", dynamic_width: false, nullable: true); ?>
          </tr>
        </form>
        <?php
          if(!empty($results))
          {
            $row_index = 0;
            foreach ($results as $result)
            {
              $row_index += 1;
              echo "<tr id='$row_index'><td style='width:20px;'>$row_index</td>";
              foreach ($result as $key => $value)
              {
                echo "<td id='".$key."'>".(string)$value."</td>";
              }
              echo "<td><button onClick='openUpdateForm($row_index)'>Update</button></td>";
              echo '</tr>';
            }
          }
        ?>
      </table>
    </div>
    <!-- End Regions -->

    <?php include "footer.php"; ?>

    <!-- End page content -->
  </div>

  <!-- Popup Form -->
  <button class="open-button" onclick="openForm()">新增資料</button>

  <div class="form-popup" id="update-form-container">
    <form method="post" action="../modules/update.php" class="form-container">
      <h1>修改客戶</h1>

      <?php echo create_inputs($conn, $current_scheme, use_default: "none"); ?>

      <!-- Create a hidden primary value for seraching the original primary -->
      <?php echo "<input type='hidden' name='origin' id='origin' value=''>"; ?>
      <input type="hidden" name="table" value="product_scheme"/>

      <button type="submit" class="btn">確認修改</button>
      <button type="button" class="btn cancel" onclick="closeUpdateForm()">Close</button>
    </form>
  </div>
  <div class="form-popup" id="insert-form-container">
    <form method="post" action="../modules/insert.php" class="form-container">
      <h1>新增客戶</h1>

      <?php 
        $input_keys = ["supplier_id","supplier_in_charge","supplier_co_name","product_name","storage_position","specification","product_unit","product_count","product_value_per_unit","total_value","import_date"];
        echo create_inputs($conn, $current_scheme, keys: $input_keys, use_default: "none"); 
      ?>

      <input type="hidden" name="table" value="product_scheme"/>
      <button type="submit" class="btn">新增</button>
      <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
    </form>
  </div>

  <?php include "javascript.php"; ?>

  </body>
</html>
