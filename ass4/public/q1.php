<?php
error_reporting(-1);
 // $newstr = filter_var($str, FILTER_SANITIZE_STRING);
include "header.html";
require_once "config.php";
class plotData{
	public static function getDeptNo($link,$dept_name){
		// echo $dept_name."<br>";
		$query = 'select dept_no from departments where dept_name="'.$dept_name.'";';
		$result = mysqli_query($link,$query);
		mysqli_fetch_all($result,MYSQLI_ASSOC);
		$dept_no=-1;
		foreach ($result as $row) {
			$dept_no=$row['dept_no'];
		}
		mysqli_free_result($result);
		return $dept_no;
	}
	public static function titles($link,$dept_no){
		$query='select title from titles as t inner join dept_emp as d d.emp_no=t.emp_no where dept_no="'.$dept_no.'";';
		$result = mysqli_query($link,$query);
		mysqli_fetch_all($result,MYSQLI_ASSOC);
		return $result;
	}
	public static function salaryTable($link,$dept_name){
		$dept_no=plotData::getDeptNo($link,$dept_name);
		if($dept_no==-1){
			echo "<b>Nothing/Invalid Data Posted!</b>";
			return false;
		}else {

			$query='select round(sum(salary)/sum(gender),0) as salary,gender,title from employees as e inner join dept_emp as d on d.emp_no = e.emp_no inner join titles as t on t.emp_no=e.emp_no inner join salaries as s on s.emp_no=e.emp_no where dept_no ="'.$dept_no.'" group by title,gender;';
			$result = mysqli_query($link,$query);
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			// <th>Male/Female</th>
			echo "<h2 align=\"center\"> Gender Pay Ratio(Avg.Female Sal./Avg. Male Sal.)</h2>";
			echo '<table> <tr>
			<th>Job Title</th>
			<th>Gender Pay Ratio(in %)</th>
		  	</tr>';
			// $titles=titles($link,$dep)
			$ar;
			$titles;
		   foreach ($result  as $row){
			 $ar[$row['title']][$row['gender']]=$row['salary'];
			 $titles[$row['title']]=$row['title'];
		   }
		   foreach($titles as $row){

			   echo "<tr><td>$row</td>";
			   $ratio= round( 100*$ar[$row]['F']/$ar[$row]['M'],2);
			   echo "<td>$ratio</td></tr>";
			//    echo "<br>";
			}
			echo '</table>';
		   mysqli_free_result($result);
		   return true;
		}
	}
	public static function allGenderRatio($link){
		$query='select gender,d.dept_no,count(gender) as count,dept_name  from  dept_emp as d inner join  departments as dep on dep.dept_no=d.dept_no inner join employees as e on e.emp_no=d.emp_no group by dep.dept_name,gender;';
		$result = mysqli_query($link,$query);
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			// <th>Male/Female</th>
			echo "<h2 align=\"center\"> Gender  Ratio(#Female / #Male)</h2>";
			echo '<div id="gradient"><table>
				<thead>
				<tr>
				<th>Department </th>
				<th>Gender Ratio(in %)</th>
				</tr>
				</thead><tbody>
			  ';
			// $titles=titles($link,$dep)
			$ar;
			$dep_name;
		   foreach ($result  as $row){
			 $ar[$row['dept_name']][$row['gender']]=$row['count'];
			 $titles[$row['dept_name']]=$row['dept_name'];
		   }
		   foreach($titles as $row){

			   echo "<tr><td>$row</td>";
			   $ratio= round( 100*$ar[$row]['F']/$ar[$row]['M'],2);
			   echo "<td>$ratio</td></tr>";
			//    echo "<br>";
			}
			echo '</tbody></table></div>';
		   mysqli_free_result($result);
		   return true;
	}
	public static function genderRatio($link,$dept_name){
		$dept_no=plotData::getDeptNo($link,$dept_name);
		if($dept_no==-1){
			echo "<b>Nothing Posted!</b>";
			return false;
		}else {
			// $query = "select gender,count(gender) from dept_emps where dept_no='$dept_no' group by gender;";
			$query='select gender,count(gender) as q from employees as e right outer JOIN dept_emp as d on e.emp_no=d.emp_no where dept_no="'.$dept_no.'" group by gender;';
			$result = mysqli_query($link,$query);
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			$ar;
			// $titles;
		   foreach ($result  as $row){
			 $ar[$row['gender']]=$row['q'];
			//  echo $row['q']."<br>";
			//  $titles[$row['title']]=$row['title'];
			//  echo "<tr>
			//  <td>".$row['gender']."</td>
			//  <td>".$row['title']."</td>
			//  <td>".$row['salary']."</td>
			//  </tr>";
		   }
		   $ratio = round(100*$ar['F']/$ar['M'],2);
		   echo '<table><tr><th>Department</th><th>Gender Ratio(in %)</th><tr><th>';
		   echo $dept_name."</th><th>";
		   echo $ratio;
		   echo"</th></tr></table>";
		   return true;
		}
	}
	public static function largestDept($link){

		$query='select count(emp_no) as count,dept_name from dept_emp  as d inner join departments as dep on d.dept_no=dep.dept_no group by d.dept_no order by count desc;';
		$result = mysqli_query($link,$query);
		mysqli_fetch_all($result,MYSQLI_ASSOC);
		echo "<h2 align=\"center\"> Department Strength</h2>";
		echo '<table> <thead><tr>
		<th width=60%>Count</th>
		<th>Deptartment</th>
		</tr></thead><tbody>';
		// $titles=titles($link,$dep)
		foreach ($result  as $row){
		 echo "<tr>
		 <td align =\"center\">".$row['count']."</td>
		 <td align =\"center\">".$row['dept_name']."</td>
		 </tr>";
		}
		echo '</tbody></table>';
		mysqli_free_result($result);
	}
	public static function tenure($link,$dept_name){
			// $query = "select gender,count(gender) from dept_emps where dept_no='$dept_no' group by gender;";
			$query='select e.emp_no as emp_no,dept_name,concat_ws(\' \',first_name,last_name) as name ,datediff(to_date,from_date) as tenure  from dept_emp as d inner join  employees as e on e.emp_no=d.emp_no inner join  departments as dep on  dep.dept_no=d.dept_no where dept_name="'.$dept_name.'" order by tenure limit 1000;';
			$result = mysqli_query($link,$query);
			mysqli_fetch_all($result,MYSQLI_ASSOC);
			if(mysqli_num_rows($result)>0){
				echo "<h2 align=\"center\">Search by Tenure</h2>";
				echo "<table>";
				echo "<thead><tr>";
				echo "<th>Employee No</th>";
				echo "<th>Name</th>";
				echo "<th>Tenure(in Days)</th>";
				echo "<th>Department</th>";
				echo "</tr></thead>";
				echo "<tbody>";
				foreach($result as $row){
					echo "<tr>";
					echo "<td>".$row['emp_no']."</td>";
					echo "<td>".$row['name']."</td>";
					echo "<td>".$row['tenure']."</td>";
					echo "<td>".$row['dept_name']."</td>";
					echo "</tr>";
				}
				echo "</tbody>";
				echo "</table>";
				return true;
			}else {
				echo "<h2>No Results Found for the given department</h2>";
				return false;
			}

	}
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$actionId=$_POST['actionId'];
	// echo $actionId;
	switch($actionId){
		case 1:
			//Employee Info
			// filter_var($str, FILTER_SANITIZE_STRING)
			$eid=filter_var($_POST['id'], FILTER_SANITIZE_STRING);
			$last_name=filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
			$dept_name=filter_var($_POST['department'], FILTER_SANITIZE_STRING);
			$query="select e.emp_no,dept_name,concat_ws(' ',first_name,last_name) as name from dept_emp as d inner join  employees as e on e.emp_no=d.emp_no inner join  departments as dep on  dep.dept_no=d.dept_no where ";
			$flag=false;

			if(!empty($last_name)){
				$flag=true;
				$query=$query."last_name=\"$last_name\" ";
			}
			if(!empty($eid)){
				if($flag){
					$query=$query." and ";
				}$flag=true;
				$query=$query."e.emp_id=\"$eid\" ";
			}
			if(!empty($dept_name)){
				if($flag){
					$query=$query." and ";
				}$flag=true;
				$query=$query."dept_name=\"$dept_name\" ";
			}
			$query=$query." ;";
			if(!$flag){
				echo "Nothing Posted!!";
			}else {
				$result = mysqli_query($link,$query);
				mysqli_fetch_all($result,MYSQLI_ASSOC);
				if(mysqli_num_rows($result) > 0){
					echo "<h2 align=\"center\"> Search Result</h2>";
					echo "<table><thead>";
					echo "<tr><th width=33%>Employee No</th>";
					echo "<th width=34%>Employee Name</th>";
					echo "<th width=34%>Department Name</th> </tr>";
					echo "</thead><tbody style=\"height:74vh;\">";
					foreach($result as $row){
						echo "<tr>";
						echo "<td align =\"center\">".$row['emp_no']."</td>";
						echo "<td align =\"center\">".$row['name']."</td>";
						echo "<td align =\"center\">".$row['dept_name']."</td>";
						echo "</tr>";
						// echo "<td>$row['emp_no']</td>";
					}
					// echo "<tfoot><tr><th width=33%>Employee No</th>";
					// echo "<th width=34%>Employee Name</th>";
					// echo "<th width=34%>Department Name</th> </tr>";
					// echo "</tfoot>";
					echo "</tbody></table>";
					mysqli_free_result($result);
				}else {
					echo "No Matches Found;";
				}
			}
		break;
		case 2:
			plotData::largestDept($link);
			// echo "sdfsdf";
		break;
		case 3:
				//by tenure;
				$dept_name=filter_var($_POST['dept_name'], FILTER_SANITIZE_STRING);
				// echo $dept_name;
				if(empty($dept_name)){
					echo "Empty Request!!";

				}else {
					plotData::tenure($link,$dept_name);
				}
		break;
		case 4:
			// $dept=trim($_POST["department"]);
			plotData::allGenderRatio($link);
			// plotData::genderRatio($link,$dept);
		break;
		case 5:
			$dept=trim($_POST["department"]);
			plotData::salaryTable($link,$dept);
		break;
		default:
		echo "Illegal Request";
		break;
	}
}else {
	echo "Illegal Request";
}
include "footer.html";
?>
