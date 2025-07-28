<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

if(isset($_POST['delete'])){
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$cart_id]);
}

if(isset($_GET['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   header('location:cart.php');
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = 'cart quantity updated';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shopping cart</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
<?php
function exec_request() {
    $url = "http://localhost:5000/api/data";
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    }

    curl_close($ch);

    echo $response;
}
?>

<!-- HTML form -->
<form method="post">
    <button type="submit" name="submit">Submit</button>
</form>

<?php
// Check if the form has been submitted
if (isset($_POST['submit'])) {
    exec_request(); // Call the function when the form is submitted
}
?>
  
<?php include 'components/user_header.php'; ?>

<section class="products shopping-cart">

   <h3 class="heading">shopping cart</h3>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);
      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
      <a href="quick_view.php?pid=<?= $fetch_cart['pid']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
      <div class="name"><?= $fetch_cart['name']; ?></div>
      <div class="flex">
         <div class="price">$<?= $fetch_cart['price']; ?>/-</div>
         <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="<?= $fetch_cart['quantity']; ?>">
         <button type="submit" class="fas fa-edit" name="update_qty"></button>
      </div>
      <div class="sub-total"> sub total : <span>$<?= $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>/-</span> </div>
      <input type="submit" value="delete item" onclick="return confirm('delete this from cart?');" class="delete-btn" name="delete">
   </form>
   <?php
   $grand_total += $sub_total;
      }
   }else{
      echo '<p class="empty">your cart is empty</p>';
   }
   ?>
   </div>

   <div class="cart-total">
      <p>grand total : <span>$<?= $grand_total; ?>/-</span></p>
      <a href="shop.php" class="option-btn">continue shopping</a>
      <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" onclick="return confirm('delete all from cart?');">delete all item</a>
      <a href="checkout.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">proceed to checkout</a>
   </div>

<h1>Voice Recognition</h1>
<p>Start speaking when you say "HELLO ASSISTANT."</p>
<audio id="audio" controls></audio>

<script>
    let audioChunks = [];
    let mediaRecorder;
    let audioContext;
    let isRecording = false;

    // Set up voice recognition
    let recognition = new window.webkitSpeechRecognition();
    recognition.continuous = true;
    recognition.lang = "en-US";

    recognition.onstart = function() {
        console.log("Listening for 'HELLO ASSISTANT'...");
    };

    recognition.onresult = function(event) {
        let last = event.results.length - 1;
        let command = event.results[last][0].transcript;

        console.log("Heard: " + command);

        if (command.toUpperCase() === "HELLO ASSISTANT") {
            if (!isRecording) {
                startRecording();
            }
        }
    };

    recognition.start();

    function startRecording() {
        isRecording = true;
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(function (stream) {
                mediaRecorder = new MediaRecorder(stream);

                mediaRecorder.ondataavailable = function (event) {
                    if (event.data.size > 0) {
                        audioChunks.push(event.data);
                    }
                };

                mediaRecorder.onstop = function () {
                    let audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                    let audioUrl = URL.createObjectURL(audioBlob);
                    document.getElementById('audio').src = audioUrl;
                };

                mediaRecorder.start();

                // Automatically stop recording if there is silence for more than 2 seconds.
                audioContext = new AudioContext();
                audioContext.createMediaStreamSource(stream).connect(audioContext.createScriptProcessor(2048, 1, 1));
                mediaRecorder.ondataavailable = function (event) {
                    audioChunks.push(event.data);
                };

                mediaRecorder.start();

                audioContext.onaudioprocess = function (event) {
                    let inputBuffer = event.inputBuffer;
                    let inputData = inputBuffer.getChannelData(0);

                    let isSilent = inputData.every(sample => Math.abs(sample) < 0.01);

                    if (isSilent) {
                        stopRecording();
                    }
                };
            })
            .catch(function (error) {
                console.error('Error accessing microphone:', error);
            });
    }

    function stopRecording() {
        isRecording = false;
        mediaRecorder.stop();
        audioContext.close();
    }
</script>












<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>