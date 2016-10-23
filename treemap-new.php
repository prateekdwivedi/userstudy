<?php
require_once 'PHPExcel/IOFactory.php';
$objPHPExcel = PHPExcel_IOFactory::load("prateek.xlsx");
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
    $worksheetTitle     = $worksheet->getTitle();
    $highestRow         = $worksheet->getHighestRow(); // e.g. 10
    $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $nrColumns = ord($highestColumn) - 64;
	
// Converting all the data in string to use array_unique function to filter unique entries
    for ($row = 2; $row <= $highestRow; ++ $row) {
        for ($col = 0; $col < 10; ++ $col) {
            $cell = $worksheet->getCellByColumnAndRow($col, $row);
            $val = $cell->getValue();
			$current_row[$col] = $val;
 }
 
 // Formation of string array, so, that we can use array_unique function to find the distinct values
	 $excel_user[$row] = $current_row[0].' '.$current_row[1];
	 $layout_details[$row] = $current_row[2].' '.$current_row[0].' '.$current_row[1].' '.$current_row[9];
	 $layout[$row] = $current_row[2];
	 $question[$row] = $current_row[0];
	 $complexity[$row] = $current_row[9];
	 $survey_user[$row] = $current_row[1];
	 $timeTaken[$row] = $current_row[2].' '.$current_row[6];
	 $correct[$row] = $current_row[2].' '.$current_row[5];
	 $dUser[$row] = $current_row[1].' '.$current_row[2];
 }
  

// ----------------------------------------------------------------------------------
// Getting Usernames
     $dUser = (array_unique($dUser));  
	 $userIndex = 0;
     foreach($dUser as $dus)
	 {
		$displayUser[$userIndex] = $dus;
		//echo $displayUser[$userIndex];
        $userIndex++;		
	 }
  
// Distinct Layouts 
      $layout = (array_unique($layout));
      $index_title = 0;
	  $i = 0;
      foreach($layout as $u)
      {
		  $layout[$index_title] = $u;
		  $correctnessCount[$index_title] = 0;
		  $totalEntry[$index_title] = 0 ;
		  $timeConsume[$index_title] = [0] ;
		//  echo $layout[$index_title].'<br>';
		  $index_title ++;
	  }
	  
// ---------------------------------------------------------------------------------
// Creating data for User display - Representing circles

      for ($xy = 0 ; $xy< $index_title ; $xy++)
			    {
				   $userData[$xy]= "";
			    }


      for($z=0; $z<sizeof($displayUser);$z++)
	  {
		   $dU = preg_split("/[\s,]+/", $displayUser[$z]);
		       $index = 0;
		       foreach ($dU as $user_display)
		        {
			   $ud_show[$index] = $user_display;
			   $index++;
		        }
			   for ($xy = 0 ; $xy< $index_title ; $xy++)
			    {
				   if($layout[$xy]== $ud_show[1])
				   {
					   if($userData[$xy] == "")
						   $userData[$xy] = '[{"userID":"'.$ud_show[0].'"}';
					   else
					       $userData[$xy] = $userData[$xy].',{"userID":"'.$ud_show[0].'"}';
				   }
			    }
	  }

	   for ($xy = 0 ; $xy< $index_title ; $xy++)
			    {
				   $userData[$xy] = $userData[$xy].']';
			    }

       

	  
// Distinct Question
      $question = (array_unique($question));
      $index_question = 0;
	  $i = 0;
      foreach($question as $u)
      {
		  $question[$index_question] = $u;
		//  echo $question[$index_question].'<br>';
		  $index_question ++;
	  }
	  

// --------------------------------------------------------------------------------	  
 
// Distinct Complexity
      $complexity = (array_unique($complexity));
      $index_complexity = 0;
	  $i = 0;
      foreach($complexity as $u)
      {
		  $complexity[$index_complexity] = $u;
		//  echo $complexity[$index_complexity].'<br>';
		  $index_complexity ++;
	  }

// --------------------------------------------------------------------------------

$colorPicker = ["#f4c2c2","#f5afaf","#f69b9b","#f78888","#f87474","#fa6161","#fb4e4e","#fc3a3a","#fd2727","#fe1313","#ff0000"];

// Time Taken
        for($z=2; $z<$row;$z++)
              {
		       $ctime = preg_split("/[\s,]+/", $timeTaken[$z]);
		       $indexTime = 0;
		       foreach ($ctime as $time_display)
		        {
			   $time_show[$indexTime] = $time_display;
			   $indexTime++;
		        }
			   for ($xy = 0 ; $xy< $index_title ; $xy++)
			    {
				   if($layout[$xy]== $time_show[0])
				   {
					   // adding the numbers
					  array_push($timeConsume[$xy],$time_show[1]);
				   }
			    }
			  } 	  
	  
	  for($xy = 0 ;$xy < $index_title ;$xy++)
	    { 
	       $timeConsume[$xy] = array_sum($timeConsume[$xy])/count($timeConsume[$xy]);
		}
		  
		  $tresholdValue = (max($timeConsume) - min($timeConsume))/10;
// --------------------------------------------------------------------------------
// Calculation of Time Complexity
$timeString = "";
for($xy = 0; $xy <$index_title; $xy++)
{
	$index = ($timeConsume[$xy] - min($timeConsume)) /$tresholdValue;
	$index = round($index);
	$timeString = $timeString.'$("#'.$layout[$xy].'").css("fill","'.$colorPicker[$index].'");';
}





// Correctness Count
        for($z=2; $z<$row;$z++)
              {
		       $ct = preg_split("/[\s,]+/", $correct[$z]);
		       $index = 0;
		       foreach ($ct as $correct_display)
		        {
			   $correct_show[$index] = $correct_display;
			   $index++;
		        }
			   for ($xy = 0 ; $xy< $index_title ; $xy++)
			    {
				   if($layout[$xy]== $correct_show[0])
				   {
					   $totalEntry[$xy]++;
					   if ($correct_show[1]==1)
						   $correctnessCount[$xy] ++ ;
					   break;
				   }
			    }
			  } 
	 
//  Preparing string for correctness
$correctString = "";

for($zu = 0 ; $zu < $index_title; $zu++)
{
	$answerCorrect = $correctnessCount[$zu] / $totalEntry[$zu];
	if($answerCorrect>=0 && $answerCorrect<0.33)
		$correctString = $correctString.'$("#'.$layout[$zu].'").css("fill","#93FF93");';
	else if($answerCorrect>=0.33 && $answerCorrect<0.70)
		$correctString = $correctString.'$("#'.$layout[$zu].'").css("fill","#00cc00");';
	else if($answerCorrect>=0.70 && $answerCorrect<0.80)
		$correctString = $correctString.'$("#'.$layout[$zu].'").css("fill","#009900");';
    else if($answerCorrect>=0.80 && $answerCorrect<0.90)
		$correctString = $correctString.'$("#'.$layout[$zu].'").css("fill","#006600");';
    else if($answerCorrect>=0.90 && $answerCorrect<1)
		$correctString = $correctString.'$("#'.$layout[$zu].'").css("fill","#003300");';
}



      $temp = null;
      $index_value = 2;
	  $subtask = 1;
	  
	  
	  foreach($layout_details as $ld)
	  {
		  $keywords = preg_split("/[\s,]+/", $ld);
		  $index = 0;
		  foreach($keywords as $tes)
          {
	       $test[$index] = $tes;
		   if($test[$index] == null)
			   $test[3] = 'easiest';
	       $index++;                         // For incrementing index variable
		  }
		  $value = $test[0].' '.$test[2].' '.$test[1];
		  
		  if($temp == null || $temp!= $value)
		  {
			  $temp = $value;
              $subtask = 1;
              $layout_details[$index_value] = $test[2].','.$test[0].' '.$test[3].' '.$test[1].' '.'subtask'.$subtask;			  
		    //  echo "Layout : ".$layout_details[$index_value]."<br>";
			  $parent[$i] = $test[0].' '.$test[3].' '.$test[1].' '.'subtask'.$subtask;
			  $i++;
			  $index_value++;
		  } 
		  else if ($temp == $value)
		  {
			  $subtask++;
			  $layout_details[$index_value] = $test[2].','.$test[0].' '.$test[3].' '.$test[1].' '.'subtask'.$subtask;
		    //  echo "Layout : ".$layout_details[$index_value]."<br>";
			  $parent[$i] = $test[0].' '.$test[3].' '.$test[1].' '.'subtask'.$subtask;
			  $i++;
			  $index_value++;
		  }
		    
	  }
	  
	  $parent = array_unique($parent);
	  $i_title = 0;
	  foreach($parent as $u)
      {
		  $lay[$i_title] = $u;
		  $i_title ++;
	  }
// To count number of user's endorsed for a given group 
// To initialize the values


// To count the circle
$count_circle = 0;	 

// To initialize the values for children 
     for($i=0; $i< $i_title; $i++) 
	 {	 
		 $children[$i] = ' ';
		 $count_children[$i] = 0;            // for values in JSON object
	 } 

// To count the number of children
     
  for($i=0; $i<$i_title;$i++)
	 {
		 for($j=2; $j< $index_value ; $j++)
		 {
			 $str_divide = preg_split("/[,]+/", $layout_details[$j]);
			 $counter = 0;
			 foreach($str_divide as $ca)
			 {
				 $testing[$counter] = $ca;
				 $counter++;
			 }
			 if ( $testing[1]== $lay[$i])
			 {
				 $children[$i]= $children[$i].' '.$testing[0];
				 $count_children[$i]++;
			 }
		 }
	 }
	 

   
   // forming the structure of JSON
   $JSON_string = '{ "name":"Root",
  "children":
[';
   for($i=0;$i<$index_title;$i++)
   {
	   $JSON_string= $JSON_string.'{"name":"'.$layout[$i].'","children":[';
	   for($j=0;$j<$index_question;$j++)
	   {
		   $JSON_string= $JSON_string.'{"name":"'.$question[$j].'","children":[';
		      for($k=0; $k<$i_title;$k++)
              {
	                   	   
		       $pt = preg_split("/[\s,]+/", $lay[$k]);
		       $index = 0;
		       foreach ($pt as $parent_display)
		       {
			   $parent_show[$index] = $parent_display;
			   $index++;
		       }
			   $comp_string = $layout[$i].' '.$question[$j];
			   $parent_string = $parent_show[0].' '.$parent_show[2];
			   if($comp_string == $parent_string)
			   {
				 if($parent_show[1] == "easiest")
					 $color = "#E5F2FF";
				 else if($parent_show[1] == "medium")
					 $color = "#60B1FF";
				 else if($parent_show[1] == "complex")
					 $color = "#1E90FF";
				 $JSON_string = $JSON_string.'{"name":"'.$parent_show[3].'","value":'.$count_children[$k].',"color":"'.$color.'"},';				 
			   }
              }
		   $JSON_string = $JSON_string.']},';	 
	   } 
	   $JSON_string = $JSON_string.']},';
   }
  
   $JSON_string= $JSON_string.']}';
   


// Contents of Circle - Representing Users

$displayCircleAsUser = "";
for($xy=0;$xy<sizeof($userData);$xy++)
{
	$displayCircleAsUser = $displayCircleAsUser.'var data ='.$userData[$xy].';
	// get x position
var currentx = $(".'.$layout[$xy].'").offset();

		var circle = d3.select(".'.$layout[$xy].'")
		            .selectAll("circle")
		            .data(data)
		            .enter()
					.append("circle")
	                .attr("cx",function(d,i){
						if(i<=10) {return currentx.left + 10+ (i * 15)+ "px"}
						else {return currentx.left + 10 + ((i-11) * 15)+ "px";}
					   ;})
					.attr("cy",function(d,i){
						if(i<=10) {return currentx.top}
						else {return currentx.top + 20 + "px"}
					   ;})
					.attr("r",5)
					.attr("class",function(d,i){
						return d.userID;
					})
					.attr("fill","yellow");
	
	';
}   
   

// --------------------------------------------------------------------------------

// HTML Contents

$print_html = '<!DOCTYPE html>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <link href="css/color-picker.min.css" rel="stylesheet">
  <link href="css/MyStyle.css" rel="stylesheet">
  <script src="js/color-picker.min.js"></script>

  <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(["_setAccount", "UA-45101494-1"]);
    _gaq.push(["_setDomainName", "delimited.io"]);
    _gaq.push(["_trackPageview"]);
    (function() {
        var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
        ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
        var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>

<div id="chart" style = "float:left;"> </div>
<div class="pickColor"><center><b>Pick for Users</b></center><br>    
<p><input type="text" id= "inputText"></p>
	<svg width="100" height="100">
     <rect width="100" height="100" onclick= "changeColor()" class="rangde" style="fill:rgb(0,0,255);stroke-width:3;stroke:rgb(0,0,0)" />
    </svg>  
 <script>

    var picker = new CP(document.querySelector("#inputText"));
    function on_start() {
        console.log("start");
    }

    function on_drag(v, instance) {
        instance.target.value = "#" + v;
        document.getElementsByClassName("rangde")[0].style.fill = "#" + v;
        document.getElementsByClassName("rangde")[0].id = "#" + v;
		console.log("drag");
    }

    function on_stop() {
        console.log("stop");
    }

    function on_enter() {
        console.log("enter");
    }

    function on_exit() {
        console.log("exit");
    }

    function on_fit() {
        console.log("fit");
    }

    function on_create(v, instance) {
        console.log("create");
        on_drag(v, instance); // trigger drag event on initiation ...
    }

    picker.on("start", on_start);
    picker.on("drag", on_drag);
    picker.on("stop", on_stop);
    picker.on("enter", on_enter);
    picker.on("exit", on_exit);
    picker.on("fit", on_fit);
    picker.on("create", on_create);

    </script></div>
	
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script src="http://d3js.org/d3.v3.min.js"></script>
  <script type = "text/javascript" src = "js/lasso.js"></script>

   <script>
   function callBack(){'
                     .$correctString.
	            	'};

   function clickEvent(){
	   '.$timeString.'
    };

	</script>
	
	<script>

		


var margin = {top: 30, right: 0, bottom: 20, left: 0},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom,
    formatNumber = d3.format(",%"),
    colorDomain = [-.6, 0, .6],
    colorRange = ["#DCDCDC", "grey", "#2F4F4F"],
    transitioning;

// sets x and y scale to determine size of visible boxes
var x = d3.scale.linear()
    .domain([0, width])
    .range([0, width]);

var y = d3.scale.linear()
    .domain([0, height])
    .range([0, height]);

// adding a color scale
var color = d3.scale.linear()
    .domain(colorDomain)
    .range(colorRange);

// introduce color scale here

var treemap = d3.layout.treemap()
    .children(function(d, depth) { return depth ? null : d._children; })
    .sort(function(a, b) { return a.value - b.value; })
    .ratio(height / width * 0.5 * (1 + Math.sqrt(5)))
    .round(false);

var svg = d3.select("#chart")
    .append("svg")
	.attr("id","drawing")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.bottom + margin.top)
    .style("margin-left", -margin.left + "px")
    .style("margin.right", -margin.right + "px")
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
    .style("shape-rendering", "crispEdges");

var grandparent = svg.append("g")
    .attr("class", "grandparent");

grandparent.append("rect")
    .attr("y", -margin.top)
    .attr("width", width)
    .attr("height", margin.top);

grandparent.append("text")
    .attr("x", 6)
    .attr("y", 6 - margin.top)
    .attr("dy", ".75em");

var legend = d3.select("#legend").append("svg")
  .attr("width", width + margin.left + margin.right)
  .attr("height", 30)
  .attr("class", "legend")
  .selectAll("g")
      .data([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18])
      .enter()
      .append("g")

// functions

function initialize(root) {
    root.x = root.y = 0;
    root.dx = width;
    root.dy = height;
    root.depth = 0;
  }

  // Aggregate the values for internal nodes. This is normally done by the
  // treemap layout, but not here because of our custom implementation.
  // We also take a snapshot of the original children (_children) to avoid
  // the children being overwritten when when layout is computed.
  function accumulate(d) {
    return (d._children = d.children)
      // recursion step, note that p and v are defined by reduce
        ? d.value = d.children.reduce(function(p, v) {return p + accumulate(v); }, 0)
        : d.value;
  }



  // Compute the treemap layout recursively such that each group of siblings
  // uses the same size (1×1) rather than the dimensions of the parent cell.
  // This optimizes the layout for the current zoom state. Note that a wrapper
  // object is created for the parent node for each group of siblings so that
  // the parent’s dimensions are not discarded as we recurse. Since each group
  // of sibling was laid out in 1×1, we must rescale to fit using absolute
  // coordinates. This lets us use a viewport to zoom.
  function layout(d) {
    if (d._children) {
      // treemap nodes comes from the treemap set of functions as part of d3
      treemap.nodes({_children: d._children});
      d._children.forEach(function(c) {
        c.x = d.x + c.x * d.dx;
        c.y = d.y + c.y * d.dy;
        c.dx *= d.dx;
        c.dy *= d.dy;
        c.parent = d;
        // recursion
        layout(c);
      });
    }
  }

function colorIncrements(d){
    return (colorDomain[colorDomain.length - 1] - colorDomain[0])/18*d + colorDomain[0];
}


legend.append("rect")
    .attr("x", function(d){return margin.left + d * 40})
    .attr("y", 0)
    .attr("fill", "#DCDCDC")
    .attr("width", "40px")
    .attr("height", "40px")


legend.append("text")
        .text(function(d){return formatNumber(colorIncrements(d))})
        .attr("y", 20)
        .attr("x", function(d){return margin.left + d * 40 + 20});

// determines if white or black will be better contrasting color
function getContrast50(hexcolor){
    return (parseInt(hexcolor.replace("#", ""), 16) > 0xffffff/3) ? "black":"white";
}

// A


var myjson = '.$JSON_string.';

root = myjson;
  console.log(root)
  initialize(root);
  accumulate(root);
  layout(root);
  display(root);

  function display(d) {
    grandparent
        .datum(d.parent)
        .on("click", transition)
      .select("text")
        .text(name(d))

    // Color header based on grandparent"s rate
    grandparent
      .datum(d.parent)
      .select("rect")
      .attr("fill", "#808080")
	  

    var g1 = svg.insert("g", ".grandparent")
        .datum(d)
        .attr("class", "depth");

    var g = g1.selectAll("g")
        .data(d._children)
      .enter().append("g")
	    .attr("class",function(d){ return d.name;});
		
//Changes made for double click
    g.filter(function(d) { return d._children; })
        .classed("children", true)
        .on("dblclick", transition);

    g.selectAll(".child")
        .data(function(d) { return d._children || [d]; })
      .enter().append("rect")
        .attr("class", "child")
        .call(rect);

    g.append("rect")
        .attr("class", "parent")
        .call(rect)
		.attr("id",function(d){ return d.name;})
      .append("title")
        .text(function(d) {console.log(typeof(d.value), d.value); return d.name + ", Number of User Entries: " + d.value ; });

	
    g.append("text")
        .attr("dy", ".75em")
        .text(function(d) { return d.name; })
        .call(text);

    function transition(d) {
      if (transitioning || !d) return;
      transitioning = true;

      var g2 = display(d),
          t1 = g1.transition().duration(750),
          t2 = g2.transition().duration(750);

      // Update the domain only after entering new elements.
      x.domain([d.x, d.x + d.dx]);
      y.domain([d.y, d.y + d.dy]);

      // Enable anti-aliasing during the transition.
      svg.style("shape-rendering", null);

      // Draw child nodes on top of parent nodes.
      svg.selectAll(".depth").sort(function(a, b) { return a.depth - b.depth; });

      // Fade-in entering text.
      g2.selectAll("text").style("fill-opacity", 0);

      // Transition to the new view.
      t1.selectAll("text").call(text).style("fill-opacity", 0);
      t2.selectAll("text").call(text).style("fill-opacity", 1);
      t1.selectAll("rect").call(rect);
      t2.selectAll("rect").call(rect);

      // Remove the old node when the transition is finished.
        t1.remove().each("end", function() {
        svg.style("shape-rendering", "crispEdges");
        transitioning = false;
      });      
    }

    return g;
  }

  function text(text) {
    text.attr("x", function(d) { return x(d.x) + 6; })
        .attr("y", function(d) { return y(d.y) + 6; })
        .attr("fill", function (d) {return getContrast50(color(parseFloat(d.rate)))});
  }

  function rect(rect) {
    rect.attr("x", function(d) { return x(d.x); })
        .attr("y", function(d) { return y(d.y); })
        .attr("width", function(d) { return x(d.x + d.dx) - x(d.x); })
        .attr("height", function(d) { return y(d.y + d.dy) - y(d.y); })
        .attr("fill", function(d){return d.color;});
  }

  function name(d) {
    return d.parent
        ? name(d.parent) + "." + d.name
        : d.name;
  }


  // Removed
  
  	
//lasso functions
		var lassoStart = function(){
			lasso.items()
				.classed({"not_possible" :true , "selected": false})
		};//end of lasso start


		
		var lassoDraw = function(){
		
			//style the possible dots
			lasso.items().filter(function(d){
				return d.possible === true;
			})
			.classed({"not_possible" : false , "possible" : true})
			
			//style the not possible dots
			lasso.items()
			     .filter(function(d){return d.possible === false;})
			.classed({"not_possible" : true , "possible" : false})
			
 	};//end of lasso draw
		


		var lassoEnd = function(){
		     
			//reset color of dots
			//style the selected dots
			lasso.items()
			     .filter(function(d){return d.selected === true;})
			     .classed({"not_possible" : false , "possible" : false,"selected" :true})
			     .attr("r" ,8);
			
			//reset the style of not selcted dots
			lasso.items().filter(function(d){
				return d.selected === false;
			})
			.classed({"not_possible" : false , "possible" : false});
		
		};//end of lasso end

  			//initiate a lasso object
			var lasso = d3.lasso()
				.items(d3.select("#drawing")
				.selectAll("circle"))
				.closePathDistance(75)
				.closePathSelect(true)
				.hoverSelect(true)
				.area(d3.select("#drawing"))
				.on("start" , lassoStart)
				.on("draw" , lassoDraw)
				.on("end" , lassoEnd);

		
		
			d3.select("#drawing").call(lasso);
  
  
  
  
 
 var svgContainer = d3.select("body").append("svg")
                                      .attr("width", 500)
                                      .attr("height", 300)
									  .attr("id","nav")
									  .append("g")
			                          .attr("transform","translate(20,0)");
 
 //Create the Scale we will use for the Axis
 var axisScale = d3.scale.linear()
                          .domain([0, 100])
                          .range([0, 400]);

 //Create the Axis
 var xAxis = d3.svg.axis()
              .scale(axisScale)
			  .orient("bottom");

 var colorScale = d3.scale.linear()
                          .domain([0, 400])
                          .range(["#ACE1AF", "#003300"]);
						  
 var colorScale2 = d3.scale.linear()
                          .domain([0, 400])
                          .range(["#F4C2C2", "#FF0000"]);
						  
var dataset2 = [0,40,80,120,160,200,240,280,320,360];

//Create an SVG group Element for the Axis elements and call the xAxis function
var xAxisGroup = svgContainer.append("g")
                              .call(xAxis);
							  
							  
var rect1 = svgContainer.selectAll("rect .correctness")
                        .data(dataset2)
						.enter()
						.append("rect")
                        .attr("x",function(d){return d;})
						.attr("y",20)
						.attr("height",20)
						.attr("width",40)
						.attr("class","correctness")
						.attr("fill",function(d){return colorScale(d);})
                        .on("mouseover",callBack);

var rect3 = svgContainer.selectAll("rect .alert")
                        .data(dataset2)
						.enter()
						.append("rect")
                        .attr("x",function(d){return d;})
						.attr("y",60)
						.attr("height",20)
						.attr("width",40)
						.attr("class","alert")
						.attr("fill",function(d){return colorScale2(d);})
                        .on("mouseover",clickEvent);	
// });


</script>


<p id="test"></p>';
 // Use a for-loop to insert circles
 echo $print_html;
   
 $user_display = "[";
$array_user = (array_unique($survey_user));

     $index_title = 0;
 
     foreach($array_user as $u)
     {
	 $user_info[$index_title] = $u;
	 $index_title ++;
     }
$index_title = 0;
$x_value = 10;
foreach ((array_count_values($survey_user)) as $display)
{
	$user_display = $user_display.'{"x_axis":'.$x_value.',"y_axis":180,"height": 10,"width":20, "color" : "blue" ,"name":"'.$user_info[$index_title].'","value":'.$display.'},';
	$index_title++;
	$x_value = $x_value +30;
}
$user_display = $user_display."]";

$user_bar = '<script>
function displayUser()
{
	'.$displayCircleAsUser.'
}

 function changeClass(abc)
 {
	alert("You have selected "+abc + " !!! ")
	$("." + abc).attr("class",abc + " RangDe");
 }

 function changeColor()
 {
 //document.getElementsByClassName("RangDe")[0].style.fill= this.id;
  var abcd = $("#inputText").val();
  $(".RangDe").css("fill",abcd);
  gayaRe();
 }
 
 // This function will reset all the classes
 function gayaRe()
  {
   var myClass = $(".RangDe").attr("class");
   var res = myClass.split(" ");
   $(".RangDe").attr("class",res[0]);
  }
 
 $( ".grandparent" ).click(function() {
  var rectReColor = rectangles
                       .style("fill", function(d) { return d.color; });
});
 

</script><script>
var jsonRectangles = '.$user_display.'
 
 var max_x = 0;
 var max_y = 0;
 var w = 500;
 var barPadding = 1;
 var colorScale = d3.scale.category20();
 for (var i = 0; i < jsonRectangles.length; i++) {
  var temp_x, temp_y;
  var temp_x = jsonRectangles[i].x_axis + jsonRectangles[i].width;
  var temp_y = jsonRectangles[i].y_axis + jsonRectangles[i].height;

  if ( temp_x >= max_x ) { max_x = temp_x; }

  if ( temp_y >= max_y ) { max_y = temp_y; }
}

var svgContainer = d3.select("body").append("svg")
                                    .attr("width", max_x)
                                    .attr("height", max_y)
				                    .attr("class","target")
									.attr("id","section")
									.on("mouseover",displayUser);
									
									
var rectangles = svgContainer.selectAll("rect")
                             .data(jsonRectangles)
                             .enter()
                             .append("rect");
							 

							 
var rectangleAttributes = rectangles
                          .attr("x", function(d, i) { return (i * 12)+ "px"; })
                          .attr("y", function (d) { return d.y_axis - d.value; })
                          .attr("height", function (d) { return d.height * d.value; })
                          .attr("width", "10px")
						  .attr("class", function(d){return d.name;})
						  .on("click",function(d){changeClass(d.name)})
                          .style("fill", function(d) { return d.color; });
						  
rectangleAttributes.append("svg:title").text(function(d){
                           return "User Id : "+d.name +" & Value : "+d.value;
						   });





</script> 



';
   echo $user_bar;
}

?>