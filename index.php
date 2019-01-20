<!doctype html>
<html lang="en">
<head>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
<title>Clay Moody Assignment 7 CPSC 862</title>

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>

<link rel="stylesheet" href="/resources/demos/style.css" />

<style>
	body { font-size: 62.5%; }
	label, input { display:block; }
	input.text { margin-bottom:12px; width:95%; padding: .4em; }
	fieldset { padding:0; border:0; margin-top:25px; }
	h1 { font-size: 1.2em; margin: .6em 0; }
	.validateTips { border: 1px solid transparent; padding: 0.3em; }
	a {
			display: block;
			border: 1px solid #fff;
			text-decoration: none;
			background-color: #fff;
			color: #123456;
			clear:both;
		}

	div#selectors {
			float:left;
			text-align: center;
			margin: 10px;
			color: blue;
		}
	div#arrows {
			float:left;
			text-align: center;
			margin: 10px;
			color: blue;
		}

	div#results {
			clear:both;
			font-size: 200.0%;
		}

	select {
			width: 140px;
			height: 600px;
			float:left;
		}

</style>

<?php
include_once("dbconnect.inc.php");

$iniFile = fopen("./sql.ini",'r');
if (!$iniFile) {
	echo "<p>Unable to open remote file.\n";
	exit;
}

while (!feof ($iniFile)) {
	$query = fgets($iniFile,1024);
	//print "$query<br>";
	if ($query!="") {
		$results = mysql_query($query);
		if(!$results) {
			echo "Could not run sql.ini file: ";
			print "</head></html>";
			exit;
		}
	}
}

echo "<script type='text/javascript'>";


$query = "select distinct Label from TheTables";
$results = mysql_query($query);
if (!$results) {
	echo "Could not get the attributes: ";
	print "</head></html>";
	exit;
}
$tableArray = array();
while ($row=mysql_fetch_row($results)) {
	array_push($tableArray,$row[0]);
}

echo "\t\t\t\tvar attr = new Array();\n";

foreach ($tableArray as $table) {
	echo "attr[\"$table\"] = new Array();\n";
}

$query = "select t.Label, a.Table1, a.Attribute, a.Label  from TheAttributes a, TheTables t where a.Table1=t.Table1";
$results = mysql_query($query);
if (!$results) {
	echo "Could not get the attributes: ";
	exit;
}
$attributesArray = array();
$i=0;
while ($row=mysql_fetch_row($results)) {
	$attributeOption = "<option value='$row[1].$row[2]'>$row[3]</option>";
	echo "attr[\"$row[0]\"].push(\"$attributeOption\");\n";
	$i++;
}

echo "</script>\n";

?>

	<script type="text/javascript">
		$().ready(function() {
			var attribute = $( "#attribute" ),
				compare = $( "#compare" ),
				operand = $( "#operand" );
			var editableAttribute = $("#edit-attribute"),
				editableCompare = $("#edit-compare"),
				editableOperand = $("#edit-operand");
			var numberPatt = /^[0-9]+$/g;
			var operandQuery;


			$('#getAttributes').click(function() {
				var table = $('#DataSets option:selected').text();
				$('#Attributes').empty();
				for (var i=0;i<attr[table].length;i++) {
					$(attr[table][i]).appendTo('#Attributes');
				}
				return;
			});
			$('#addOutput').click(function() {
				return $('#Attributes option:selected').clone().appendTo('#Output');
			});
			$('#addOrderBy').click(function() {
				return $('#Attributes option:selected').clone().appendTo('#OrderBy');
			});

			$('#delOutput').click(function() {
				var c = confirm("Are you sure you want to delete?");
				if (c==true)
					return $('#Output option:selected').remove();
				else return;
			});

			$('#delOrderBy').click(function() {
				var c = confirm("Are you sure you want to delete?");
				if (c==true)
					return $('#OrderBy option:selected').remove();
				else return;
			});
			$('#delConstraint').click(function() {
				var c = confirm("Are you sure you want to delete?");
				if (c==true)
					return $('#Constraints option:selected').remove();
				else return;
			});
			$('#moveUpOutput').click(function() {
				$("#Output option:selected").each(function() {
					var listItem = $(this);
					var listItemPosition = $("#Outputs option").index(listItem) + 1;
					if (listItemPosition == 1) return false;
					listItem.insertBefore(listItem.prev());
				});
			});
			$('#moveUpOrderBy').click(function() {
				$("#OrderBy option:selected").each(function() {
					var listItem = $(this);
					var listItemPosition = $("#OrderBy option").index(listItem) + 1;
					if (listItemPosition == 1) return false;
					listItem.insertBefore(listItem.prev());
				});
			});

			$('#moveDownOutput').click(function () {
				var itemsCount = $("#Output option").length;

				$($("#Output option:selected").get().reverse()).each(function() {
					var listItem = $(this);
					var listItemPosition = $("#Output option").index(listItem) + 1;
					if (listItemPosition == itemsCount) return false; 
					listItem.insertAfter(listItem.next());
				});
			});
			$('#moveDownOrderBy').click(function () {
				var itemsCount = $("#OrderBy option").length;

				$($("#OrderBy option:selected").get().reverse()).each(function() {
					var listItem = $(this);
					var listItemPosition = $("#OrderBy option").index(listItem) + 1;
					if (listItemPosition == itemsCount) return false; 
					listItem.insertAfter(listItem.next());
				});
			});

			$('#AscDscOrderBy').click(function () {
				var label = $('#OrderBy option:selected').html();
				var value = $('#OrderBy option:selected').val();
				var patt=/DESC/g;
				if (label.match(patt)) {
					label = label.substr(0,label.lastIndexOf(" DESC"));
					$('#OrderBy option:selected').text(label);
					value = value.substr(0,val.lastIndexOf(" DESC"));
					$('#OrderBy option:selected').val(value);
				} else {
					label = label + " DESC";
					$('#OrderBy option:selected').text(label);
					value = value + " DESC";
					$('#OrderBy option:selected').val(value);
				}
				return;
			});
			$('#submitQuery').click(function () {
				var outputCount = $("#Output option").length;
				if (outputCount==0) {
					alert ("Output cannot be empty");
				} else {
					$('#Output option').each(function(i) {
						$(this).attr("selected", "selected");
					});
					$('#OrderBy option').each(function(i) {
						$(this).attr("selected", "selected");
					});
					$('#Constraints option').each(function(i) {
						$(this).attr("selected", "selected");
					});
					$('#resultsOrSQL').val("1");
					$('#target').submit();
				}
			});

			$('#viewsql').click(function () {
				var outputCount = $("#Output option").length;
				if (outputCount==0) {
					alert ("Output cannot be empty");
				} else {
					$('#Output option').each(function(i) {
						$(this).attr("selected", "selected");
					});
					$('#OrderBy option').each(function(i) {
						$(this).attr("selected", "selected");
					});
					$('#Constraints option').each(function(i) {
						$(this).attr("selected", "selected");
					});
					$('#resultsOrSQL').val("2");
					$('#target').submit();
				}
			});

			$( "#constraint-form" ).dialog({
				autoOpen: false,
				height: 300,
				width: 350,
				modal: true,
				buttons: {
					"Add Constraint": function() {
						var bValid;
						if (!operand.val().search(" ")>=0) {
							bValid = true;
						} else {
							alert ("No spaces allowed in constaint value box");
							operand.val("");
						}
						var opString;
						if ( bValid ) {
							var selectedAttr = $('#Attributes option:selected').text();
							var selectedOption = $('#Attributes option:selected').val();
							operandQuery = operand.val();
							if (compare.val().search("like")>=0) { operandQuery = "%"+operand.val()+"%";}
							else if (compare.val().search("null")>=0) { operandQuery = ""; operand.val("");}
							else if (operand.val().search(numberPatt)<0) { operandQuery = "!"+operand.val()+"!"; }
							var valueString = selectedOption+ " "+compare.val()+" "+operandQuery;
							var optionString = selectedAttr+" "+compare.val()+" "+operand.val();
							$("<option value='"+valueString+"'>"+optionString+"</option>").appendTo("#Constraints");
							$( this ).dialog( "close" );
						}
					},

					Cancel: function() {
						$( this ).dialog("close");
					}
				},

				close: function() {
					attribute.val("");
					compare.val("");
					operand.val("");
				}
			});


			$( "#addConstraints" ).click(function() {
				$( "#constraint-form" ).dialog( "open" );
				selectedAttr = $('#Attributes option:selected').text();
				attribute.val(selectedAttr);
				return;
			});

			$( "#edit-constraint-form" ).dialog({
				autoOpen: false,
				height: 300,
				width: 350,
				modal: true,
				buttons: {
					"Save Constraint": function() {
						var bValid;
						if (!operand.val().search(" ")>=0) {
							bValid = true;
						} else {
							alert ("No spaces allowed in constaint value box");
							operand.val("");
						}
						var opString;
						var selConstraintOption = $("#Constraints option:selected").text().trim();
						var selConstraintValue = $("#Constraints option:selected").val().trim();
						var constraintValueFields = selConstraintValue.split(" ");
						var constraintOptionFields = selConstraintOption.split(" ");
						var attributeString, selValueString = constraintValueFields[0];
						var myI;
						operandQuery = editableOperand.val();
						if (constraintValueFields.length == 4) { 		
							attributeString = "";
							for (myI=0;myI<(constraintOptionFields.length-3);myI++) {
									attributeString = attributeString + constraintOptionFields[myI] + " ";
								}

						} else {
							attributeString = "";
							for (myI=0;myI<(constraintOptionFields.length-2);myI++) {
								attributeString = attributeString + constraintOptionFields[myI] + " ";
							}
						}

						if (editableCompare.val().search("like")>=0) { operandQuery = "%"+editableOperand.val()+"%";}
						else if (editableCompare.val().search("null")>=0) { operandQuery = ""; }
						else if (editableOperand.val().search(numberPatt)<0) { operandQuery = "!"+editableOperand.val()+"!"; }
						
						var valueString = selValueString+ " "+editableCompare.val()+" "+operandQuery;
						var optionString = attributeString+" "+editableCompare.val()+" "+editableOperand.val();

						$("#Constraints option:selected").remove();
						$("<option value='"+valueString+"'>"+optionString+"</option>").appendTo("#Constraints");
						$( this ).dialog( "close" );
					},

					Cancel: function() {
						$( this ).dialog("close");
					}
				},

				close: function() {
					editableAttribute.val("");
					editableCompare.val("");
					editableOperand.val("");
				}
			});



			$("#editConstraint").click(function() {
				var selConstraintOption = $("#Constraints option:selected").text().trim();
				var selConstraintValue = $("#Constraints option:selected").val().trim();
				var constraintValueFields = selConstraintValue.split(" ");
				var constraintOptionFields = selConstraintOption.split(" ");
				var attributeString;
				var myI, selectedComparable;
				var nullCompare=0;

				if (constraintValueFields[constraintValueFields.length-1] == "null") {
					nullCompare = 1;
				}

				if (constraintValueFields.length == 4) {
					attributeString = "";
					for (myI=0;myI<(constraintOptionFields.length-3);myI++) {
						attributeString = attributeString + constraintOptionFields[myI] + " ";
					}
					if (constraintValueFields[constraintValueFields.length-1] == "null") {
						selectedComparable = constraintValueFields[constraintValueFields.length-3] + " " +
						constraintValueFields[constraintValueFields.length-2] + " " +
						constraintValueFields[constraintValueFields.length-1] ;
					} else if (constraintValueFields[constraintValueFields.length-2] == "like") {
						selectedComparable = constraintValueFields[constraintValueFields.length-3] + " " +
						constraintValueFields[constraintValueFields.length-2];
					}
				} else {
					attributeString = "";
					for (myI=0;myI<(constraintOptionFields.length-2);myI++) {
						attributeString = attributeString + constraintOptionFields[myI] + " ";
					}
					if (constraintValueFields[constraintValueFields.length-1] == "null") {
						selectedComparable = constraintValueFields[constraintValueFields.length-2] + " " +
						constraintValueFields[constraintValueFields.length-1] ;
					} else {
						selectedComparable = constraintValueFields[constraintValueFields.length-1];
					}

				}

				editableAttribute.val(attributeString);
				if (nullCompare==0) editableOperand.val(constraintOptionFields[constraintOptionFields.length-1]);
				else editableOperand.val("");
				editableCompare.val(selectedComparable);
				$("#edit-constraint-form").dialog("open");

			});

			



	}); //end ready


	</script>


</head>

<body>
<h1>W. Clay Moody, Assignment 7, CPSC 862, Spring 2013</h1>

<!hidden sections>

	<div id="OutputDelDialog" style="display: none;">
		Are you sure you want to delete the selected outputs?
	</div>
	
	<div id="constraint-form" title="Define Constraint" style="display: none;">
		<p>All form fields are required.</p>
			<form>
				<input type="text" id="attribute" name="attribute" size="35">
				<select id="compare" name="compare" style="height:10px; width:100px;">
					<option value="=">Is Equal to</option>
					<option value="<>">Is not Equal to</option>
					<option value=">">Greater than</option>
					<option value=">=">Greater than or Equal to</option>
					<option value="<">Less than</option>
					<option value="<=">Less than or Equal to</option>
					<option value="like">Contains</option>
					<option value="not like">Does Not Contain</option>
					<option value="is null">Is Blank</option>
					<option value="is not null">Is not Blank</option>
				</select>
				<input type="text" id="operand" name="operand" size="35">
			</form>
	</div>

	<div id="edit-constraint-form" title="Edit Constraint" style="display: none;">
		<p>All form fields are required.</p>
			<form>
				<input type="text" id="edit-attribute" name="edit-attribute" size="35">
				<select id="edit-compare" name="edit-compare" style="height:10px; width:100px;">
					<option value="=">Is Equal to</option>
					<option value="<>">Is not Equal to</option>
					<option value=">">Greater than</option>
					<option value=">=">Greater than or Equal to</option>
					<option value="<">Less than</option>
					<option value="<=">Less than or Equal to</option>
					<option value="like">Contains</option>
					<option value="not like">Does Not Contain</option>
					<option value="is null">Is Blank</option>
					<option value="is not null">Is not Blank</option>
				</select>
				<input type="text" id="edit-operand" name="edit-operand" size="35">
			</form>
	</div>


<form enctype="multipart/form-data" id="target" action="index.php" method="post">
	<input type="hidden" name="resultsOrSQL" id="resultsOrSQL" value="0">
	<div id="selectors">
		<h3>Data Sets</h3>
		<select multiple id="DataSets">

<?php
$query = "select Table1, Label from TheTables";
$results = mysql_query($query);
if (!$results) {
	print "Error getting Tables Listing<br>";
	exit;
}
while ($row=mysql_fetch_row($results)) {
	print "\t\t<option value='$row[0]'>$row[1]</option>\n";
}
?>

		</select>
		<br><br><br>
		<a href="#" id="viewsql"><img src="buttons/viewsql.png" height="44" width="100"></a>
	</div>
	<div id="arrows">
		<a href="#" id="getAttributes"><img src="buttons/right.png" height="44" width="69" style="margin-top:300px"></a>
	</div>
	<div id="selectors">
		<h3>Attributes</h3>
		<select multiple id="Attributes"></select>
		<br><br><br>
		<a href="#" id="submitQuery"><img src="buttons/submit.png" height="44" width="100"></a>
	</div>
	<div id="arrows">
		<a href="#" id="addOutput"><img src="buttons/right.png" height="44" width="69" style="margin-top:100px"></a>
		<a href="#" id="addOrderBy"><img src="buttons/right.png" height="44" width="69" style="margin-top:160px"></a>
		<a href="#" id="addConstraints"><img src="buttons/right.png" height="44" width="69" style="margin-top:180px"></a>
	</div>
	<div id="selectors">
		<h3>Outputs</h3>
		<select multiple id="Output" name="output[]" style='height: 175px;width: 240px;'></select>
		<h3>Order By</h3>
		<select multiple id="OrderBy" name="orderby[]" style='height: 175px;width: 240px;'></select>
		<h3>Constraints</h3>
		<select multiple id="Constraints" name="constraints[]" style='height: 175px; width: 240px;'></select>
	</div>
	<div id="arrows">
		<a href="#" id="moveUpOutput"><img src="buttons/up.png" height="22" width="35" style="margin-top:90px"></a>
		<a href="#" id="moveDownOutput"><img src="buttons/down.png" height="22" width="35"></a>
		<a href="#" id="delOutput"><img src="buttons/del.png" height="22" width="35"></a>

		<a href="#" id="moveUpOrderBy"><img src="buttons/up.png" height="22" width="35" style="margin-top:100px"></a>
		<a href="#" id="moveDownOrderBy"><img src="buttons/down.png" height="22" width="35"></a>
		<a href="#" id="AscDscOrderBy"><img src="buttons/ascdsc.png" height="22" width="50"></a>
		<a href="#" id="delOrderBy"><img src="buttons/del.png" height="22" width="35"></a>

		<a href="#" id="editConstraint"><img src="buttons/edit.png" height="22" width="35" style="margin-top:125px"></a>
		<a href="#" id="delConstraint"><img src="buttons/del.png" height="22" width="35"></a>
	</div>

	<!input type="submit">

</form>

<div id="results">


<?php

$keepOnGoing=False;
if (isset($_POST['resultsOrSQL'])) {
	$showResults=$_POST['resultsOrSQL'];
	if ($showResults!="0")
		$keepOnGoing=True;
}
if ($keepOnGoing) {

	$tableArray = array();
	if (isset($_POST['output'])) {
		$output=$_POST['output'];
		if ($output){
			foreach ($output as $t){
				$tab = substr($t, 0, strpos($t, '.'));
				if (!in_array($tab,$tableArray)) {
					array_push($tableArray,$tab);
				}

			}
		}
	}
	else $output = array();
	if (isset($_POST['orderby'])) {
		$orderby=$_POST['orderby'];
		if ($orderby){
			foreach ($orderby as $t){
				$tab = substr($t, 0, strpos($t,'.'));
				if (!in_array($tab,$tableArray)) {
					array_push($tableArray,$tab);
				}
			}
		}
	}
	else $orderby = array();
	if (isset($_POST['constraints'])) {
		$constraints=$_POST['constraints'];
		if ($constraints){
			foreach ($constraints as $t){
				$tab = substr($t, 0, strpos($t,'.'));
				if (!in_array($tab,$tableArray)) {
					array_push($tableArray,$tab);
				}
			}
		}
	}
	else $constraints= array();

	$outputString = "";
	if (!empty($output)) {
		foreach ($output as $out) {
			$outputString .= "$out, ";
		}
		$outputString = substr_replace($outputString ,"",-2);
	}
//	print "output string: $outputString <br>";


	//create list of joins from TheJoins between tables that are used

	$tableQuery = "SELECT * from TheJoins";
	$result = mysql_query($tableQuery);
	if (!$result) {
		echo "Could not get Table information: " . mysql_error();
		exit;
	}
	$joinArray = array();
	while($row=mysql_fetch_row($result)) {
	        if (!array_key_exists($row[0],$joinArray)) {
        	        $joinArray[$row[0]] = array();
        	}
		$joinArray[$row[0]][$row[1]] = $row[2];
	}
	$joinConstraints = array();
	for ($index=0;$index<count($tableArray);$index++) {
		for ($i=$index+1; $i<count($tableArray);$i++) {
			$joinToSplit = $joinArray[$tableArray[$index]][$tableArray[$i]];
			$joins = explode("|",$joinToSplit);
			foreach ($joins as $j) {
				if (!in_array($j,$joinConstraints)) array_push($joinConstraints,$j);
			}
		}
	}

	$joinString = "";
	$patt = '/(.+)\..+=(.+)\..+/';
	foreach ($joinConstraints as $j) {
		$joinString .= "$j and ";
		if (preg_match($patt, $j, &$matches)>0) {
			if (!in_array($matches[1],$tableArray)) array_push($tableArray, $matches[1]);
			if (!in_array($matches[2],$tableArray)) array_push($tableArray, $matches[2]);
		}
	}
	$joinString = substr_replace($joinString,"",-5);

	//calculate which tables were used
	// do this after reviewing TheJoins
	$tableString = "";
	foreach ($tableArray as $table) {
		$tableString .= "$table, ";
	}
	$tableString = substr_replace($tableString, "", -2);
//	echo "Table String is $tableString <br>";

	$orderbyString = "";
	if (!empty($orderby)) {
		foreach ($orderby as $order) {
			$orderbyString .= "$order, ";
		}
		$orderbyString = substr_replace($orderbyString ,"",-2);
	}
//	print "orderby string: $orderbyString <br>";

	$constraintString="";
	if (!empty($constraints)) {
		foreach ($constraints as $con) {
			$patt = '/like %(.*)%/';
			$patt2 = '/!(.*)!/';
			if (strstr($con,"%")) $con2 = preg_replace($patt, "like '%$1%'",$con);
			else if (strstr($con,"!")) $con2 = preg_replace($patt2, "'$1'",$con);
			else $con2 = $con;
			$constraintString .= "$con2 and ";
		}
		$constraintString = substr_replace($constraintString ,"",-5);
	}

/*
	echo "Here is where we are:<br>";
	echo "Output String: $outputString <br><br>";
	echo "Table String: $tableString <br><br>";
	echo "Join String: $joinString <br><br>";
	echo "Constraint String: $constraintString <br><br>";
	echo "Order By String: $orderbyString <br><br>";
*/
	$query = "SELECT $outputString";
	if ($tableString!="") $query .= " FROM $tableString";
	if (($joinString!="") || ($constraintString!="")) {
		$query .= " WHERE";
		if ($joinString!="") $query .= " $joinString";
		if (($joinString!="") && ($constraintString!="")) $query .= " and";
		if ($constraintString!="") $query .= " $constraintString";
	}
	if ($orderbyString!="") $query .= " ORDER BY $orderbyString";

	//echo "The query is $query";

if ($showResults==2) {
	$sqlPopUp = '<script type="text/javascript">var query = "The SQL Query is ';
	$sqlPopUp .= $query;
	$sqlPopUp  .= '";';
	echo "$sqlPopUp";
	echo "alert(query)\n</script>\n";
}


if ($showResults==1) {

	// retrieve query results
	$result = mysql_query($query);
	if (!$result) {
    		echo 'Could not run query: ' . mysql_error();
		exit;
	}



	// print
	print "<br><br>The following data has been retrieved from the database<br><br>";
	$first = 1;

	echo "<table border='1'>";

	while($row=mysql_fetch_assoc($result))
	{

		if ($first==1) {
			$first = 0;
			echo "<tr bgcolor='blue'>";
			foreach (array_keys($row) as $field) {
				print "<td><font color='white'>$field&nbsp</font></td>";
			}
			echo "</tr>";
		}
		echo "<tr>";
		foreach ($row as $element) {
			print "<td>$element&nbsp</td>";
		}
		print "</tr>";
	}

	echo "</table>";
	mysql_free_result($result);


} //end of show results


} // end of keep on going


?>
</div>
</body></html>

