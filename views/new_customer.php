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
    html,body,h1,h2,h3,h4,h5 { font-family: "Consolas", sans-serif }
    </style>
  </head>
  <?php
    ini_set('display_errors', 1);
    require_once "../config.php";

    require_once DB_CONNECT;
    require_once DATA_CUSTOMER;
    include_once VIEW_HEADER;


    $selection = $customer_scheme->GetPostValues(true, "%");

    $modifying = TryGetValue("modifying", null);
    $modifying = $modifying != null;


    $offset = TryGetValue("offset", "0");

    $results = $customer_scheme->Select($conn, "*", $selection, offset: $offset);
    $current_page = CurrentPage();

    $counts = $customer_scheme->CountRows($conn, 'age');
  ?>
  <body class="w3-light-grey">

  <?php include "top_bar.php" ?>

  <!-- Sidebar/menu -->
  <nav class="w3-sidebar w3-collapse w3-white" style="z-index:3;width:300px;" id="mySidebar"><br>
    <div class="w3-container w3-row">
      <div class="w3-col s4">
        <img src="/w3images/avatar2.png" class="w3-circle w3-margin-right" style="width:46px">
      </div>
      <div class="w3-col s8 w3-bar">
        <span>Welcome, <strong>Mike</strong></span><br>
        <a href="#" class="w3-bar-item w3-button"><i class="fa fa-envelope"></i></a>
        <a href="#" class="w3-bar-item w3-button"><i class="fa fa-user"></i></a>
        <a href="#" class="w3-bar-item w3-button"><i class="fa fa-cog"></i></a>
      </div>
    </div>
    <hr>
    <div class="w3-container">
      <h5>資料庫</h5>
    </div>
    <div class="w3-bar-block">
      <a href="new_customer.php" class="w3-bar-item w3-button w3-padding w3-blue"><i class="fa material-icons fa-fw">people</i>  客戶基本資料</a>
      <a href="new_order.php" class="w3-bar-item w3-button w3-padding"><i class="fa material-icons fa-fw">pending_actions</i>  客戶訂貨記錄</a>
      <a href="new_product.php" class="w3-bar-item w3-button w3-padding"><i class="fa material-icons fa-fw">widgets</i>  公司進貨</a>
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
                echo $counts;
              ?>
            </h3>
          </div>
          <div class="w3-clear"></div>
          <h4>使用者</h4>
        </div>
      </div>
      <div class="w3-quarter">
        <div class="w3-container w3-blue w3-padding-16">
          <div class="w3-left"><i class="fa material-icons w3-xxxlarge">contact_page</i></div>
          <div class="w3-right">
            <h3>
              <?php
                echo $customer_scheme->Average($conn, 'age');
              ?>
            </h3>
          </div>
          <div class="w3-clear"></div>
          <h4>Average Age</h4>
        </div>
      </div>
      <!-- <div class="w3-quarter">
        <div class="w3-container w3-teal w3-padding-16">
          <div class="w3-left"><i class="fa fa-share-alt w3-xxxlarge"></i></div>
          <div class="w3-right">
            <h3>23</h3>
          </div>
          <div class="w3-clear"></div>
          <h4>Shares</h4>
        </div>
      </div>
      <div class="w3-quarter">
        <div class="w3-container w3-orange w3-text-white w3-padding-16">
          <div class="w3-left"><i class="fa fa-users w3-xxxlarge"></i></div>
          <div class="w3-right">
            <h3>50</h3>
          </div>
          <div class="w3-clear"></div>
          <h4>Users</h4>
        </div>
      </div> -->
    </div>
    <br>

    <!-- End Dash board -->

    <!-- Regions -->

    <div class="w3-medium" style="overflow: scroll; white-space: nowrap">
      <div class="w3-container">
        <h5>Datas</h5>
      </div>
      <table class="w3-table w3-striped w3-white" >
        <form method="post" action="" class="form-container">
          <tr>
            <td></td>
            <?php echo create_headers($customer_scheme); ?>
          </tr>
          <tr>
            <td><input type="submit" style='width:50px;'></td>
            <?php

              echo create_inputs($conn, $customer_scheme, use_default: "post", dynamic_width: false, nullable: true); 
            ?>
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
    <?php 
          $left_page = $offset - 10;
          $right_page = $offset + 10;
          if($left_page >= 0)
          {
            echo "<a href='$current_page?offset=$left_page' style='margin: 35px;'>上一頁</a>";
          }
          else
          {
            echo "<del style='margin: 35px;'>上一頁</del>";
          }
          $current = 0;
          $page = 1;
          while($current < $counts)
          {
            echo "<a href='$current_page?offset=$current' style='margin: 5px;'>$page</a>";
            $page++;
            $current+=10;
            echo "   ";
          }
         
          if($right_page < $counts)
          {
            echo "<a href='$current_page?offset=$right_page' style='margin: 35px;'>下一頁</a>";
          }
          else
          {
            echo "<del style='margin: 35px;'>下一頁</del>";
          }
        ?>
    <!-- End Regions -->
    <!--
    <hr>
    <div class="w3-container">
      <h5>General Stats</h5>
      <p>New Visitors</p>
      <div class="w3-grey">
        <div class="w3-container w3-center w3-padding w3-green" style="width:25%">+25%</div>
      </div>

      <p>New Users</p>
      <div class="w3-grey">
        <div class="w3-container w3-center w3-padding w3-orange" style="width:50%">50%</div>
      </div>

      <p>Bounce Rate</p>
      <div class="w3-grey">
        <div class="w3-container w3-center w3-padding w3-red" style="width:75%">75%</div>
      </div>
    </div>
    <hr>

    <div class="w3-container">
      <h5>Countries</h5>
      <table class="w3-table w3-striped w3-bordered w3-border w3-hoverable w3-white">
        <tr>
          <td>United States</td>
          <td>65%</td>
        </tr>
        <tr>
          <td>UK</td>
          <td>15.7%</td>
        </tr>
        <tr>
          <td>Russia</td>
          <td>5.6%</td>
        </tr>
        <tr>
          <td>Spain</td>
          <td>2.1%</td>
        </tr>
        <tr>
          <td>India</td>
          <td>1.9%</td>
        </tr>
        <tr>
          <td>France</td>
          <td>1.5%</td>
        </tr>
      </table><br>
      <button class="w3-button w3-dark-grey">More Countries  <i class="fa fa-arrow-right"></i></button>
    </div>
    <hr>
    <div class="w3-container">
      <h5>Recent Users</h5>
      <ul class="w3-ul w3-card-4 w3-white">
        <li class="w3-padding-16">
          <img src="/w3images/avatar2.png" class="w3-left w3-circle w3-margin-right" style="width:35px">
          <span class="w3-xlarge">Mike</span><br>
        </li>
        <li class="w3-padding-16">
          <img src="/w3images/avatar5.png" class="w3-left w3-circle w3-margin-right" style="width:35px">
          <span class="w3-xlarge">Jill</span><br>
        </li>
        <li class="w3-padding-16">
          <img src="/w3images/avatar6.png" class="w3-left w3-circle w3-margin-right" style="width:35px">
          <span class="w3-xlarge">Jane</span><br>
        </li>
      </ul>
    </div>
    <hr>

    <div class="w3-container">
      <h5>Recent Comments</h5>
      <div class="w3-row">
        <div class="w3-col m2 text-center">
          <img class="w3-circle" src="/w3images/avatar3.png" style="width:96px;height:96px">
        </div>
        <div class="w3-col m10 w3-container">
          <h4>John <span class="w3-opacity w3-medium">Sep 29, 2014, 9:12 PM</span></h4>
          <p>Keep up the GREAT work! I am cheering for you!! Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><br>
        </div>
      </div>

      <div class="w3-row">
        <div class="w3-col m2 text-center">
          <img class="w3-circle" src="/w3images/avatar1.png" style="width:96px;height:96px">
        </div>
        <div class="w3-col m10 w3-container">
          <h4>Bo <span class="w3-opacity w3-medium">Sep 28, 2014, 10:15 PM</span></h4>
          <p>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><br>
        </div>
      </div>
    </div>
    <br>
    <div class="w3-container w3-dark-grey w3-padding-32">
      <div class="w3-row">
        <div class="w3-container w3-third">
          <h5 class="w3-bottombar w3-border-green">Demographic</h5>
          <p>Language</p>
          <p>Country</p>
          <p>City</p>
        </div>
        <div class="w3-container w3-third">
          <h5 class="w3-bottombar w3-border-red">System</h5>
          <p>Browser</p>
          <p>OS</p>
          <p>More</p>
        </div>
        <div class="w3-container w3-third">
          <h5 class="w3-bottombar w3-border-orange">Target</h5>
          <p>Users</p>
          <p>Active</p>
          <p>Geo</p>
          <p>Interests</p>
        </div>
      </div>
    </div>
  -->

  <?php include "footer.php"; ?>

    <!-- End page content -->
  </div>

  <!-- Popup Form -->
  <button class="open-button" onclick="openForm()">新增資料</button>

  <div class="form-popup" id="update-form-container">
    <form method="post" action="../modules/update.php" class="form-container">
      <h1>修改客戶</h1>

      <?php echo create_inputs($conn, $customer_scheme, use_default: "none"); ?>

      <!-- Create a hidden primary value for seraching the original primary -->
      <?php echo "<input type='hidden' name='origin' id='origin' value=''>"; ?>
      <input type="hidden" name="table" value="customer_scheme"/>

      <button type="submit" class="btn">確認修改</button>
      <button type="button" class="btn cancel" onclick="closeUpdateForm()">Close</button>
    </form>
  </div>
  <div class="form-popup" id="insert-form-container">
    <form method="post" action="../modules/insert.php" class="form-container">
      <h1>新增客戶</h1>

      <?php 
        $input_keys = ['name', 'id', 'phone', 'address', 'age', 'profession', 'photo'];
        echo create_inputs($conn, $customer_scheme, keys: $input_keys, use_default: "none"); 
      ?>

      <input type="hidden" name="table" value="customer_scheme"/>
      <input type="hidden" name="consumption_state" value="啟用"/>
      <button type="submit" class="btn">新增</button>
      <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
    </form>
  </div>

  <script type="text/javascript">

    var primary = <?php echo json_encode($customer_scheme->FindPrimary()->db_name); ?>;
    var primary_value = null;
    var db_names = <?php echo json_encode($customer_scheme->GetDBNames()); ?>;

    function openForm() {
      document.getElementById("insert-form-container").style.display = "block";
    }

    function closeForm() {
      document.getElementById("insert-form-container").style.display = "none";
    }

    function openUpdateForm(row_id)
    {
      document.getElementById("update-form-container").style.display = "block";

      var form = document.getElementById("update-form-container").children[0];

      var element = document.getElementById(row_id);
      for(var col_node=element.firstChild; col_node!==null; col_node=col_node.nextSibling)
      {
        if(col_node.id == primary)
        {
          primary_value = col_node.innerHTML;
        }
        for(var i=0; i < form.children.length; i++)
        {
          if(form.children[i].id == col_node.id)
          {
            
            if(db_names.includes(col_node.id))
            {
              form.children[i].value = col_node.innerHTML;
            }

          }
          if(form.children[i].id == "origin")
          {
            form.children[i].value = primary_value;
          }
        }
      }
    }

    function closeUpdateForm()
    {
      document.getElementById("update-form-container").style.display = "none";
    }

    function validate(max_length = 0, regex = "")
    {

    }
  </script>

  <!-- End Popup Form -->

  <script>
  // Get the Sidebar
  var mySidebar = document.getElementById("mySidebar");

  // Get the DIV with overlay effect
  var overlayBg = document.getElementById("myOverlay");

  // Toggle between showing and hiding the sidebar, and add overlay effect
  function w3_open() {
    if (mySidebar.style.display === 'block') {
      mySidebar.style.display = 'none';
      overlayBg.style.display = "none";
    } else {
      mySidebar.style.display = 'block';
      overlayBg.style.display = "block";
    }
  }

  // Close the sidebar with the close button
  function w3_close() {
    mySidebar.style.display = "none";
    overlayBg.style.display = "none";
  }
  </script>

  </body>
</html>
