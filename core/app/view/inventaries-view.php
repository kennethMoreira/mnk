
<section class="content">
<div class="row">
	<div class="col-md-12">
<!-- Single button -->

		<h1><i class="glyphicon glyphicon-stats"></i> Inventario Global</h1>
<ol class="breadcrumb">
  <li><a href="./?view=home">Inicio</a></li>
  <li><a href="./?view=stocks">Sucursales</a></li>
</ol>

<a onclick="thePDF()" class="btn btn-default">Descargar PDF</a><br><br>
<?php
$products = ProductData::getAll();
$sucursales = StockData::getAll();
if(count($products)>0){
	?>
<div class="clearfix"></div>
<div class="box">
  <div class="box-header">
    <h3 class="box-title">Inventario Global</h3>

  </div><!-- /.box-header -->
  <div class=" table table-responsive  box-body">
  <table class="table-condensed table-bordered datatable table-hover">
	<thead>
		<th>Codigo</th>
		<th>Producto</th>
    <th>Marca</th>
    <!-- <th>Categoria</th>
    <th>Tipo</th> -->
    <th>Modelo</th>
    
    
    <?php foreach($sucursales as $suc):?>
		<th><?php echo $suc->name; ?></th>
    <?php endforeach; ?>
    <th>Total</th>
	</thead>
	<?php foreach($products as $product):?>
	<tr>
		<td><?php echo $product->code; ?></td>
		<td><?php echo $product->name; ?></td> <?php $total=0; ?>
    
      


    <td><?php if($product->brand_id!=null){echo $product->getBrand()->name;}else{ echo "----"; }  ?></td>
    <!-- <td><?php if($product->category_id!=null){echo $product->getCategory()->name;}else{ echo "----"; }  ?></td>
    <td><?php echo $product->kind; ?></td> -->
    <td><?php echo $product->barcode; ?></td>
    <?php foreach($sucursales as $suc):?>
		<td>
			<?php 
  $q=OperationData::getQByStock($product->id,$suc->id);
  $total=$total + $q ;
      echo $q; ?>
		</td>
    <?php endforeach; ?>
    <td> <?php echo $total; ?>
	</tr>
	<?php endforeach;?>
</table>
  </div><!-- /.box-body -->
</div><!-- /.box -->



<div class="clearfix"></div>

	<?php
}else{
	?>
	<div class="jumbotron">
		<h2>No hay productos</h2>
		<p>No se han agregado productos a la base de datos, puedes agregar uno dando click en el boton <b>"Agregar Producto"</b>.</p>
	</div>
	<?php
}

?>
<br><br><br><br><br><br><br><br><br><br>
	</div>
</div>
</section>






<script type="text/javascript">
        function thePDF() {
var doc = new jsPDF('p', 'pt');
        doc.setFontSize(26);
        doc.text("<?php echo ConfigurationData::getByPreffix("company_name")->val;?>", 40, 65);
        doc.setFontSize(18);
        doc.text("INVENTARIO GLOBAL", 40, 80);
        doc.setFontSize(12);
        doc.text("Usuario: <?php echo Core::$user->name." ".Core::$user->lastname; ?>  -  Fecha: <?php echo date("d-m-Y h:i:s");?> ", 40, 90);

var columns = [
//    {title: "Reten", dataKey: "reten"},
    {title: "Codigo", dataKey: "code"}, 
    {title: "Nombre del Producto", dataKey: "product"}, 
    {title: "Marca", dataKey: "brand_id"}, 
    // {title: "Modelo", datakey: "category_id"},
    // {title: "Tipo", datakey: "kind"},
    <?php foreach($sucursales as $suc):?>
    {title: "<?php echo $suc->name; ?>", dataKey: "suc-<?php echo $suc->id; ?>"}, 

    <?php endforeach; ?>
//    ...
];



var rows = [
  <?php foreach($products as $product):
  ?>
    {
      "code": "<?php echo $product->code; ?>",
      "product": "<?php echo $product->name; ?>",
      "brand_id":"<?php if($product->brand_id!=null){echo $product->getBrand()->name;}else{ echo "----"; }  ?>",
      // "category_id":"<?php if($product->category_id!=null){echo $product->getCategory()->name;}else{ echo "----"; }  ?>",
      // "kind":"<?php echo $product->kind;?>",
    <?php foreach($sucursales as $suc):
      $q=OperationData::getQByStock($product->id,$suc->id);?>
      "suc-<?php echo $suc->id; ?>": "<?php echo $q;?>",
      <?php endforeach; ?>
      },
 <?php endforeach; ?>
];


doc.autoTable(columns, rows, {
    theme: 'grid',
    overflow:'linebreak',
    styles: {
        fillColor: <?php echo Core::$pdf_table_fillcolor;?>
    },
    columnStyles: {
        id: {fillColor: <?php echo Core::$pdf_table_column_fillcolor;?>}
    },
    margin: {top: 100},
    afterPageContent: function(data) {
//        doc.text("Header", 40, 30);
    }
});

doc.setFontSize(12);
doc.text("<?php echo Core::$pdf_footer;?>", 40, doc.autoTableEndPosY()+25);

<?php 
$con = ConfigurationData::getByPreffix("report_image");
if($con!=null && $con->val!=""):
?>

var img = new Image();
img.src= "storage/configuration/<?php echo $con->val;?>";
img.onload = function(){
doc.addImage(img, 'PNG', 495, 20, 60, 60,'mon');	
doc.save('inventary-<?php echo date("d-m-Y h:i:s",time()); ?>.pdf');
}
<?php else:?>
doc.save('inventary-<?php echo date("d-m-Y h:i:s",time()); ?>.pdf');
<?php endif; ?>


//doc.output("datauri");

        }
    </script>