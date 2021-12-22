<script type="text/javascript">

    var primary = <?php echo json_encode($current_scheme->FindPrimary()->db_name); ?>;
    var primary_value = null;
    var db_names = <?php echo json_encode($current_scheme->GetDBNames()); ?>;

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

      var element = document.getElementById(row_id.toString());

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
            console.log("Find");
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

  // When the user clicks on the button,
  //toggle between hiding and showing the dropdown content
  function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
  }

  function filterFunction(id) {
    var input, filter, ul, li, a, i;
    console.log("search-drop-down-"+id);
    input = document.getElementById("search-drop-down-"+id);
    filter = input.value.toUpperCase();
    div = document.getElementById("myDropdown");
    a = div.getElementsByTagName("a");
    for (i = 0; i < a.length; i++) {
      txtValue = a[i].textContent || a[i].innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        a[i].style.display = "";
      } else {
        a[i].style.display = "none";
      }
    }
  }
  </script>