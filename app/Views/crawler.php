<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Crawler Test</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url();?>/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-3.3.1.js"></script>
   
    <!-- Bootstrap Datatables -->
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap4.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
	<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet">
	<link href="//cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo base_url();?>/assets/bootstrap/js/popper.min.js"></script>
	
	<!-- Bootstrap core JavaScript -->
	<script src="<?php echo base_url();?>/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    
	<!-- MDB core JavaScript -->
    <script type="text/javascript" src="<?php echo base_url();?>/assets/bootstrap/js/mdb.min.js"></script>

    <!-- Custom styles -->
    <link href="<?php echo base_url();?>/assets/css/styles.css" rel="stylesheet">

  </head>
  <body>
  <!-- Navigation -->
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark fixed-top" style="background-color:#000000;">
    <div class="container">
		<div class="d-flex flex-row justify-content-center align-middle">
			<a href="<?php echo base_url();?>"><h3 class="ml-3" style="color:white;">Crawler Test: standvirtual.com</h3></a>
		</div>
    </div>
  </nav>
  
  <!-- Default Crawler Buttons to call Ajax Functions -->
  <div align="center" class="card-body text-center mt-3">
	<h2>
		<button type="button" class="btn btn-info" id="vehicleCrawler">Start Crawl Vehicles (<?php echo $qVehicles["totalRows"];?>/<?php echo $qUrls["totalRows"];?>)</button>
		<a href="<?php echo base_url();?>" class="btn btn-success">Show Results</a>
	</h2>
  </div>

  <!-- Shows datatable with Vehicle Information -->
  <div class="card mt-3" style="background-color:#d8d8d8;">
	  <div class="card-body">
			<table align="center" id="table" class="table table-striped table-bordered mt-4" style="width:90%;background-color:white;">
				<thead>
					<tr class="success">
						<th>ID</th>
						<th>FULL.NAME</th>
						<th>RETAIL.PRICE</th>
						<th>BRAND</th>
						<th>MODEL</th>
						<th>BODY.TYPE</th>
						<th>PORTUGUESE.VERSION</th>
						<th>GEARS</th>
						<th>YEAR</th>
						<th>MONTH</th>
						<th>KM</th>
						<th>ENGINE</th>			
					</tr>
				</thead>
				<tbody>
					<?php foreach ($vehicles as $row){?>
					<tr>
						<td><?php echo $row->id;?></td>
						<td><a target="_blank" class="text-blue" href="<?php echo $row->url;?>"><?php echo $row->brand.' '.$row->model.' '.$row->engine.' '.$row->portugueseVersion.' '.$row->year;?> <i class="fa fa-search"></i></a></td>
						<td><?php echo number_format($row->retailPrice,2,',','.');?></td>
						<td><?php echo $row->brand;?></td>
						<td><?php echo $row->model;?></td>
						<td><?php echo $row->bodyType;?></td>
						<td><?php echo $row->portugueseVersion;?></td>
						<td><?php echo $row->qGear;?></td>
						<td><?php echo $row->year;?></td>
						<td><?php echo $row->month;?></td>
						<td><?php echo $row->km;?></td>
						<td><?php echo $row->engine;?></td>
					</tr>
					<?php }?>
				</tbody>
			</table> 
		</div>
	</div>

	<script>
	/* Ajax recursive function to search for valid vehicle urls to be crawled */
	var page = 0;
	function crawlUrls(){
		$.ajax({
			url: 'index.php/UrlController/crawlUrls',
			type: 'post',
			data: {'page':page},
			success:function(data){
				page++;
				crawlUrls();
			},
			error: function (request, status, error) {
				crawlUrls();
			}
		})
	}
	
	/* Ajax recursive function to crawl vehicles from the urls queue */
	function crawlVehicles(){
		$.ajax({
			url: 'index.php/VehicleController/crawlVehicle',
			type: 'post',
			success:function(data){
				$("#vehicleCrawler").html('Processing... ('+data+')');
				crawlVehicles();
			},
			error: function (request, status, error) {
				crawlVehicles();
			}
		})
	}


	$(document).ready(function() {
		/* Starts crawling urls and vehicles data */
		$("#vehicleCrawler").click(function(){
			$("#vehicleCrawler").html('Processing...');
			$("#vehicleCrawler").attr("disabled", true);
			crawlUrls();
			crawlVehicles();
		});

		/* Calls bootstrap datatable functions */
		var table = $('#table').DataTable( {
			"order": [[ 0, "desc", 3, "desc" ]],
			lengthChange: true,
					buttons: [
					{
						extend: 'copyHtml5',
						exportOptions: {
							columns: ':visible'
						}
					},
					{
						extend: 'excelHtml5',
						exportOptions: {
							columns: ':visible'
						}
					},
					{
						extend: 'pdfHtml5',
						exportOptions: {
							columns: ':visible'
						}
				},
				'colvis'
			],
			"lengthMenu": [[-1], ['Showing Last 100 Vehicles Crawled']],
			'columnDefs': [
				{ targets: [3,4,6,11], visible: false }
			],
			iDisplayLength: -1,
			"autoWidth": true,
			"responsive": true,
				 "language": {
						"searchPlaceholder": "Key word...",
						"sLengthMenu": "_MENU_"
				}
				});
			table.buttons().container()
				.appendTo( '#table_wrapper .col-md-6:eq(0)' );
		});
	</script>	
</div>

 <!-- Footer -->
  <footer class="py-5" style="background-color:#000;">
    <div class="container">
      <p class="m-0 text-center text-white">Web Crawler Test &reg; <?php echo date('Y');?> </p>
    </div>
  </footer>
</body>
</html>