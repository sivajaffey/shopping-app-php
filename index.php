<!DOCTYPE html>
<html>
	<style>
		.container {
			display:flex;
			flex-direction:row;
		}
		.body {
			width: 70%;
			height:100%;
		}
		.sidebar {
			width:30%;
			height:100%;
		}
		.products-list {
			display:flex;
			flex-direction:row;
			flex-wrap: wrap;
			margin:10px;
			padding:10px;
		}
		.item-block {
			padding:15px;
			border:1px solid;
			border-radius:20px;
			margin:15px;
		}
	</style>
	<?php
	$conn = connectDB();
	$count = 0;
	session_start();

function connectDB() {
	$servername = "localhost"; // set hostname
	$username = "siva"; // set usename
	$password = "5100"; // set password
	$dbname = "practice"; // set database name
	$port = 3306; // set port number

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname,$port);
	// Check connection
	if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
	}

	return $conn;
}

Class Queries {
	public $array;
	function __contruct() {
	}
	
	function setArr($array) {
		$this->array = $array;
	}
	function registerSql($conn) {
		if ((trim($this->array['username']) !== "") && (trim($this->array['password']) !== "")) {
			$count = $this->getUserCount($conn);
			if ($count == 0) {
				$hash = password_hash(trim($this->array['password']),PASSWORD_DEFAULT);
				$sql="INSERT INTO `users` (`username`, `password`) VALUES ('".trim($this->array['username'])."','".trim($hash)."')";
				$res = mysqli_query($conn,$sql);
				header("Refresh:0");
			}
		}
	}
	function delete($conn) {
		if ((trim($this->array['id']) !== "")) {
			$sql="DELETE from users WHERE id='".htmlspecialchars(trim($this->array['id']))."'";
			$res = mysqli_query($conn,$sql);
			header("Refresh:0");
		}
	}
	function login($conn) {
		if ((trim($this->array['username']) !== "") && (trim($this->array['password']) !== "")) {
			$sql = "SELECT * FROM users WHERE username='".trim($this->array['username'])."'";
			$res = mysqli_query($conn,$sql);
			$data = mysqli_fetch_assoc($res);
			if ($res->num_rows == 1) {
				$verify = password_verify(trim($this->array['password']),$data['password']);
				if ($verify) {
					$_SESSION['username'] = $data['username'];
					$_SESSION['id'] = $data['id'];
				}
			}
		}
	}
	function getUserCount($conn) {
		if ((trim($this->array['username']) !== "") && (trim($this->array['password']) !== "")) {
			$sql = "SELECT username FROM users WHERE username='".htmlspecialchars(trim($this->array['username']))."'";
			$res = mysqli_query($conn,$sql);
			return $res->num_rows;
		} else {
			return 0;
		}	
	}
	function addProduct($conn) {
		if ((trim($this->array['product_name']) !== "") && (trim($this->array['product_description']) !== "") && (trim($this->array['product_available']) !== "")) {
			$sql="INSERT INTO `products` (`product_name`, `product_description`, `product_available`, `user_id`) VALUES ('".trim($this->array['product_name'])."','".trim($this->array['product_description'])."', '".trim($this->array['product_available'])."','".trim($this->array['user_id'])."')";
			$res = mysqli_query($conn,$sql);
			header("Refresh:0");
		}
	}
	function deleteProduct($conn) {
		if ((trim($this->array['id']) !== "")) {
			$sql="DELETE from products WHERE id='".trim($this->array['id'])."'";
			$res = mysqli_query($conn,$sql);
			header("Refresh:0");
		}
	}
	function buyProduct($conn) {
		if ((trim($this->array['id']) !== "")) {
			$sql="UPDATE products SET user_id='".trim($this->array['user_id'])."' WHERE id='".trim($this->array['id'])."'";
			print_r($sql);
			$res = mysqli_query($conn,$sql);
			header("Refresh:0");
		}
	}
}

// api methods
function RegisterUser($conn) {
	$sql = new Queries();
	$sql->setArr($_POST);
	$res = $sql->registerSql($conn);
}
function DeleteUser($conn) {
	$sql = new Queries();
	$sql->setArr($_POST);
	$res = $sql->delete($conn);
}
function LoginUser($conn) {
	$sql = new Queries();
	$sql->setArr($_POST);
	$res = $sql->login($conn);
}
function AddProduct($conn) {
	$sql = new Queries();
	$sql->setArr($_POST);
	$res = $sql->addProduct($conn);
}
function DeleteProduct($conn) {
	$sql = new Queries();
	$sql->setArr($_POST);
	$res = $sql->deleteProduct($conn);
}
function buyProduct($conn) {
	$sql = new Queries();
	$sql->setArr($_POST);
	$res = $sql->buyProduct($conn);
}
if ($_POST) {
	if ($_POST['register'] && $count < 5) {
		RegisterUser($conn);
	} elseif ($_POST['delete']) {
		DeleteUser($conn);
	} else if ($_POST['login']) {
		LoginUser($conn);
	} else if ($_POST['add_product']) {
		AddProduct($conn);
	} else if ($_POST['delete_product']) {
		DeleteProduct($conn);
	} else if ($_POST['buy']) {
		buyProduct($conn);
	}
}
	?>

<body>

<h1>Shopping App PHP</h1>
<div class="container">
	<!-- Purchase Products starts -->
	<div class="body">
		<h3>Products</h3>
		<div class="products-list">
		<?php
			$sql = "SELECT product_name,id,product_description,product_available,user_id FROM products";
			$result = mysqli_query($conn, $sql);
			$row = mysqli_num_rows($result);
			$count = $row;
			if ($row > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$buy;
				if (isset($_SESSION['id'])) {
					$buy = "<button type='submit' name='buy' value='buy'>Buy</button>";
				}
				if ($row['user_id'] != $_SESSION['id']) {
					echo "<div class='item'>
					<form action='' method='post'>
						<div class='item-block'>
							<h4>".$row['product_name']."</h4>
							<div>Available : ".$row['product_available']."</div>
							<p>Description : ".$row['product_description']."</p>
							<input type='hidden' name='user_id' value='".$_SESSION['id']."' />
							<input type='hidden' name='id' value='".$row['id']."' />
							$buy
						</div>
					</form>
				</div>";
				}
			}
			} else {
				echo "0 results";
			}
		?>
		</div>
	</div>
	<!-- Purchase Products ends -->
	<div class="sidebar">
			<!-- With Sesssion start -->
			<div>
			
			<?php if (isset($_SESSION['id'])) { 
				echo "Welcome ".$_SESSION['username'];
				if (isset($_POST['logout'])) {
					session_destroy();
					header("Refresh:0");
				}
			?>
				<div><form action="" method="post"><button type="submit" name="logout" value="logout">Logout</a></form></div>
				<div class="add-product">
					<h2>Add product</h2>
					<div>
						<form action="" method="post">
							<div><input type="text" name="product_name" max="50" placeholder="Enter product title" /></div>
							<div><textarea type="text" name="product_description" max="100" placeholder="Enter description for product"></textarea></div>
							<input type="hidden" name="user_id" value="<?php echo $_SESSION['id'];?>" />
							<div><input type="number" placeholder="Enter Quantity" name="product_available"/></div>
							<div><button type="submit" name="add_product" value="add_product">Add</button></div>
						<form>
					</div>
					<div>
						
					</div>
				</div>
			
				<table>
					<thead>
						<tr><h2>Product List</h2><tr>
						<tr><th>ID</th><th>Product name</th><th>Description</th><th>Quantity</th><th>Delete</th></tr>
					</thead>
					<tbody>
						<?php
							$sql = "SELECT product_name,id,product_description,product_available FROM products WHERE user_id='".$_SESSION['id']."'";
							$result = mysqli_query($conn, $sql);
							$row = mysqli_num_rows($result);
							$count = $row;
							if ($row > 0) {
							while($row = mysqli_fetch_assoc($result)) {
								echo "<tr><form action='' method='post'><td>" . $row["id"]. "</td><td>"."". $row["product_name"]."</td><td>"."". $row["product_description"]."</td><td>"."". $row["product_available"]."</td><td><input type='hidden' name='id' value='".$row["id"]."'><button type='submit' name='delete_product' value='delete_product'>Delete</button></td></form></tr>";
							}
							} else {
								echo "0 results";
							}
						?>
						
					</tbody>
				</table>
				<?php } ?>
			</div>
			<!-- With Sesssion ends -->
		<!-- Without Session starts-->
		<?php if (!isset($_SESSION['id'])) { ?>
		<form action="" method="post" >
			<h2>Register Here</h2>
			<p>You can add max 5 users only</p>
			<div>
				<label>username</label>
				<input type="text" name="username" required/>
			<div>
			<div>
				<label>password</label>
				<input type="password" name="password" required/>
			</div>
			<div>
				<button type="submit"  name="register" value="Register">Register</button>
			</div>	
		</form>
		<form action="" method="post">
			<h2>Login Here</h2>
			<div>
				<label>username</label>
				<input type="text" name="username"/>
			<div>
			<div>
				<label>password</label>
				<input type="password" name="password"/>
			</div>
			<div>
				<button type="submit" name="login" value="Login">Login</button>
			</div>
		</form>
		<table>
			<thead>
				<tr><h2>Users List</h2><tr>
				<tr><th>ID</th><th>Name</th><th>Delete</th></tr>
			</thead>
			<tbody>
				<?php
				$sql = "SELECT * FROM users ORDER BY username";
				$result = mysqli_query($conn, $sql);
				$row = mysqli_num_rows($result);
				$count = $row;
				if ($row > 0) {
				while($row = mysqli_fetch_assoc($result)) {
					echo "<tr><form action='' method='post'><td>" . $row["id"]. "</td><td>"."". $row["username"]."</td><td><input type='hidden' name='id' value='".$row["id"]."'><button type='submit' name='delete' value='delete'>Delete</button></td></form></tr>";
				}
				} else {
					echo "0 results";
				}
				?>
				
			</tbody>
		</table>
		<?php } ?>
		<!-- Without Session ends -->
	</div>
	
</div>

<?php

?>

</body>
</html>