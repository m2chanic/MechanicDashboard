<?php
const baseFireStore = "https://firestore.googleapis.com/v1/projects/";
$api_key = 'AIzaSyBjVQ4qjUUO42eqPL_gPkDU_DDuhT9_4ms';
$project_id = 'm2chanic';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css" type=" text/css" />
    <style>

        .chart {
            width: 300px;
            max-height: 200px; /* Set your desired max height */
            overflow-y: hidden; /* Initially hide the scrollbar */
            margin: 20px auto;
            border: 1px solid #ccc;
            padding: 10px;
            box-sizing: border-box;
            transition: overflow-y 0.5s; /* Add a smooth transition for the overflow property */
        }

        .chart:hover {
            overflow-y: auto; /* Show scrollbar on hover */
        }
        .bar {
            background-color: #FBCD08FF;
            height: 20px;
            margin-bottom: 5px;
            transition: width 1s ease-in-out;
        }

        .label {
            margin-bottom: 5px;
            color: white;
            background:#362E17FF ;
        }
        .chart-con{
            width: 100% ;
            text-align:  center;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #chart-container {
            margin-top: 20px;
        }
    </style>
    <script type="module">

        if(localStorage.getItem('currentUser')==null){
            window.location.href = 'login.html';
        }

        import {
            initializeApp
        } from 'https://www.gstatic.com/firebasejs/10.5.2/firebase-app.js';
        import {
            getAuth,
            signOut,
            createUserWithEmailAndPassword
        } from 'https://www.gstatic.com/firebasejs/10.5.2/firebase-auth.js';
        import {
            getStorage,
            ref,
            uploadBytes,
            getDownloadURL
        } from 'https://www.gstatic.com/firebasejs/10.5.2/firebase-storage.js';
        import {
            getFirestore,
            collection,
            addDoc,
            doc,
            serverTimestamp,
            getDocs,
            getDoc,
            updateDoc,
            deleteDoc,
            setDoc,
            query,
            where
        } from 'https://www.gstatic.com/firebasejs/10.5.2/firebase-firestore.js';
        const firebaseConfig = {
            apiKey: "AIzaSyBjVQ4qjUUO42eqPL_gPkDU_DDuhT9_4ms",
            authDomain: "m2chanic.firebaseapp.com",
            projectId: "m2chanic",
            storageBucket: "m2chanic.appspot.com",
            messagingSenderId: "905844914008",
            appId: "1:905844914008:web:9f027ce3dfc2c142da572d",
            measurementId: "G-5WYG8GY8SS"
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const storage = getStorage(app);
        const db = getFirestore(app);

        function registerServiceProvider() {
            const serviceProviderName = document.getElementById('serviceProviderName').value;
            const phoneNumber = document.getElementById('phoneNumber').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('providerPassword').value;
            const district = document.getElementById('districtInput').value;
            const streetName = document.getElementById('streetNameInput').value;
            const city = document.getElementById('cityInput').value;
            if (serviceProviderName && phoneNumber && email && password && district && streetName && city) {
                createUserWithEmailAndPassword(auth, email, password)
                    .then((userCredential) => {
                        const user = userCredential.user;
                        const serviceProviderRef = doc(db, 'ServiceProvider', user.uid);
                        const newServiceProvider = {
                            'uid': user.uid,
                            'name': serviceProviderName,
                            'phoneNumber': phoneNumber,
                            'email': email,
                            'automativeRepair': {
                                'district': district,
                                'streetName': streetName,
                                'city': city
                            }
                        };
                        setDoc(serviceProviderRef, newServiceProvider)
                            .then(() => {
                                console.log('Document written with ID: ', user.uid);
                                alert('Registration successful!');
                                closeAddWorkshopModal();
                                location.reload();
                                // You can redirect to another page or perform other actions here
                            })
                            .catch((error) => {
                                console.error('Error adding document: ', error);
                                alert('Registration failed. Please try again.');

                            });
                    })
                    .catch((error) => {
                        const errorCode = error.code;
                        const errorMessage = error.message;
                        console.error(errorMessage);
                        if (error.code === 'auth/email-already-in-use') {
                            alert('The email address is already in use by another account.');
                        } else {
                            console.error('Error adding document: ', error);
                            alert('Registration failed. Please try again.');
                        }
                    });
            } else {
                alert('Please fill out all the required fields.');
            }
        }

        function addSlideshowImage() {
            const file = document.getElementById('imageInput').files[0];
            const storageRef = ref(storage, 'images/' + file.name);

            // Upload file to Firebase Storage
            uploadBytes(storageRef, file).then((snapshot) => {
                console.log('Uploaded a blob or file!');

                // Get the download URL of the uploaded file
                getDownloadURL(snapshot.ref).then((downloadURL) => {
                    // Save image URL and current date in Firestore collection "ads"
                    const adsRef = collection(db, 'ads');
                    const newAd = {
                        imgurl: downloadURL,
                        date: serverTimestamp()
                    };
                    addDoc(adsRef, newAd)
                        .then((docRef) => {
                            console.log('Document written with ID: ', docRef.id);
                            const slideshow = document.getElementById('slideshow');
                            const slide = document.createElement('div');
                            slide.classList.add('slide');
                            const img = document.createElement('img');
                            img.src = downloadURL;
                            img.alt = 'Slide';
                            const button = document.createElement('button');
                            button.classList.add('button');
                            button.textContent = 'Delete';
                            button.onclick = function() {
                                deleteSlideItem(docRef.id, this);
                            };
                            slide.appendChild(img);
                            slide.appendChild(button);
                            slideshow.appendChild(slide);
                            document.getElementById('imageInput').value = null;
                        })
                        .catch((error) => {
                            console.error('Error adding document: ', error);
                        });
                });
            });
        }

        document.getElementById('add-slid-btn').addEventListener('click', function() {
            addSlideshowImage();

        });
        document.querySelector('.add-btn').addEventListener('click', function() {
            registerServiceProvider();

        });

        async function deleteSlideItem(slideId, button) {
            try {
                await deleteDoc(doc(db, 'ads', slideId));
                console.log('Document with ID', slideId, 'successfully deleted.');
                // Remove the slide element from the DOM
                const slide = button.parentNode;
                slide.remove();
            } catch (error) {
                console.error('Error removing document: ', error);
            }
        }

        const slides = document.querySelectorAll('.slide');
        slides.forEach(slide => {
            const slideId = slide.querySelector('.slide-id').value;
            console.log(slideId);
            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Delete';
            deleteButton.addEventListener('click', () => deleteSlideItem(slideId, deleteButton));
            slide.appendChild(deleteButton);
        });

        const shopRows = document.querySelectorAll('.shop-row');
        shopRows.forEach(shop => {
            const serviceProviderId = shop.querySelector('.service-id').value;
            console.log("Service Provider" + serviceProviderId);
            const td = shop.querySelector('.block-td');
            const i = document.createElement('i');
            i.classList.add('fas', 'fa-ban');
            i.setAttribute('data-service-provider', serviceProviderId);
            i.addEventListener('click', () => toggleWorkshopStatus(serviceProviderId));
            const workshopRef = doc(db, 'ServiceProvider', serviceProviderId); // Replace with your collection name
            getDoc(workshopRef).then((doc) => {
                if (doc.exists()) {
                    const currentStatus = doc.data().blocked;
                    i.style.color = currentStatus ? 'red' : 'black';
                }
            });
            td.appendChild(i);
            shop.appendChild(td);
        });

        function toggleWorkshopStatus(serviceProviderId) {
            const workshopRef = doc(db, 'ServiceProvider', serviceProviderId); // Replace with your collection name
            getDoc(workshopRef).then((doc) => {
                if (doc.exists()) {
                    const currentStatus = doc.data().blocked;
                    if (!currentStatus) {
                        updateDoc(workshopRef, {
                            blocked: true
                        });
                        console.log(`Automative repair shop ${serviceProviderId} blocked.`);
                        // Perform additional actions as needed for blocking the workshop
                        updateIconToBlock(serviceProviderId);

                    } else {
                        updateDoc(workshopRef, {
                            blocked: false
                        });
                        console.log(`Automative repair shop ${serviceProviderId} unblocked.`);
                        // Perform additional actions as needed for unblocking the workshop
                        updateIconToUnblock(serviceProviderId);
                    }
                } else {
                    console.log(`Automative repair shop ${serviceProviderId} not found.`);
                }
            }).catch((error) => {
                console.error('Error getting workshop document: ', error);
            });
        }
        function logout(){
            signOut(auth).then(() => {
                localStorage.removeItem('currentUser');
                window.location.reload()  ;
                console.log('User signed out successfully.');
            }).catch((error) => {
                // An error happened.
                console.error('An error occurred during sign-out:', error);
            });
        }
        document.getElementById('signoutLink').addEventListener('click', function() {
            console.log('successfully.');
            logout();
        });


    </script>
</head>

<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
    </div>
    <div class="sidebar">
        <img src="assets/images/applogo.jpeg" alt="logo" class="logo">
        <a href="#dashboard" onclick="transitionTo('dashboard')" id="dashboardLink">
            <i class="fas fa-tachometer-alt"></i>
            <span>Statistics</span>
        </a>
        <a href="#ad" onclick="transitionTo('ad')" id="adLink">
            <i class="fas fa-ad"></i>
            <span>Ad Management</span>
        </a>
        <a href="#workshop" onclick="transitionTo('workshop')" id="workshopLink">
            <i class="fas fa-tools"></i>
            <span>Automative repair </span>
        </a>
        <a id="signoutLink" onchange="logout()">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sign Out</span>
        </a>
    </div>

    <div class="content">
        <div class="card" id="loading" style="width: 100%;height:500px;">
            <center> <img class="loading-img" src="assets/images/loading.gif"></img></center> 
        </div>
        <?php

        $service_providers_url = "https://firestore.googleapis.com/v1/projects/{$project_id}/databases/(default)/documents/ServiceProvider?key={$api_key}";

        $chProvider = curl_init();
        curl_setopt($chProvider, CURLOPT_URL, $service_providers_url);
        curl_setopt($chProvider, CURLOPT_RETURNTRANSFER, 1);

        $service_providers_response = curl_exec($chProvider);

        if (curl_errno($chProvider)) {
            echo 'Error:' . curl_error($chProvider);
        } else {
            $service_provider_names = array();
            $drivers_counts = array();
            $service_providers_data = json_decode($service_providers_response, true);

            if (isset($service_providers_data['documents'])) {
                foreach ($service_providers_data['documents'] as $service_provider) {
                    $service_provider_name = $service_provider['fields']['name']['stringValue'];
                    $service_provider_id = $service_provider['fields']['uid']['stringValue'];
                    $drivers_url = "https://firestore.googleapis.com/v1/projects/{$project_id}/databases/(default)/documents/ServiceProvider/{$service_provider_id}/Drivers?key={$api_key}";
                    $chDrivers = curl_init();
                    curl_setopt($chDrivers, CURLOPT_URL, $drivers_url);
                    curl_setopt($chDrivers, CURLOPT_RETURNTRANSFER, 1);
                    $drivers_response = curl_exec($chDrivers);
                    $drivers_data = json_decode($drivers_response, true);
                    $drivers_count = 0;

                    if (isset($drivers_data['documents'])) {
                        $drivers_count = count($drivers_data['documents']);
                    }

                    array_push($service_provider_names, $service_provider_name);
                    array_push($drivers_counts, $drivers_count);

                    curl_close($chDrivers);
                }
            } else {
                echo "No ServiceProviders found.";
            }
        }

        curl_close($chProvider);

        ?>

        <div class="card" id="dashboard">
            <div class="card-header">Statistics</div>
            <!-- Display Statistics -->

            <div class="statistics">
                <div class="chart-con">
                <div class="container">
                    <h1>Payment Statistics Dashboard</h1>
                    <div id="stats-container">
                        <!-- Display statistics here -->
                    </div>
                    <div id="chart-container">
                        <canvas id="payment-chart"></canvas>
                    </div>
                </div>
                </div>
                <div class="chart statistic" onclick="showRepairShops()">
                    <div class="sub-title">
                        <?php
                        $counts = [];
                        foreach ($drivers_counts as $driver) {
                            $counts[] = $driver; // Fixed the array assignment
                        }

                        $index = 0;
                        foreach ($service_provider_names as $name) {
                            $words = explode(' ', $name);
                            ?>
                            <div class="bar" style="width: <?php echo (($counts[$index] / sizeof($drivers_counts)) * 100)+2 ?>%;"></div>
                            <div class="label"><?php echo $name ?></div>
                            <?php $index++;
                        }
                        ?>
                    </div>
                </div>
                <script>
                    // JavaScript to trigger the animation after the page loads
                    window.addEventListener('load', function () {
                        // Select all elements with the class 'bar' and add a class to trigger the animation
                        var bars = document.querySelectorAll('.bar');
                        bars.forEach(function (bar) {
                            bar.classList.add('animate');
                        });
                    });
                </script>
             <!--   <div class="statistic" onclick="showRepairShops()">
                    <i class="fas fa-warehouse"></i>
                    <h3>Automative repair shops</h3>
                    <p>200</p>
                </div>-->
                <div class="statistic">
                    <i class="fas fa-users"></i>
                    <h3>Users</h3>
                    <p><?php

                        $countUrl = baseFireStore . "{$project_id}/databases/(default)/documents/users?key={$api_key}";

                        $chCount = curl_init();

                        curl_setopt($chCount, CURLOPT_URL, $countUrl);
                        curl_setopt($chCount, CURLOPT_RETURNTRANSFER, 1);

                        $responseCount = curl_exec($chCount);

                        if (curl_errno($chCount)) {
                            echo '0';
                        } else {
                            $data = json_decode($responseCount, true);
                            $count = count($data['documents']);
                            echo $count;
                        }

                        curl_close($chCount);
                        ?></p> <!-- Replace with actual number of users -->
                </div>
                <div class="statistic">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Orders</h3>
                    <p>

                        <?php

                        $users_url = baseFireStore . "{$project_id}/databases/(default)/documents/users?key={$api_key}";

                        $chOrders = curl_init();
                        curl_setopt($chOrders, CURLOPT_URL, $users_url);
                        curl_setopt($chOrders, CURLOPT_RETURNTRANSFER, 1);

                        $users_response = curl_exec($chOrders);

                        if (curl_errno($chOrders)) {
                            echo 'Error:' . curl_error($chOrders);
                        } else {
                            $users_data = json_decode($users_response, true);
                            $total_orders_count = 0;
                            if (isset($users_data['documents'])) {
                                foreach ($users_data['documents'] as $user) {
                                    $user_id = basename($user['name']);
                                    $orders_url = baseFireStore . "{$project_id}/databases/(default)/documents/users/{$user_id}/orders?key={$api_key}";
                                    curl_setopt($chOrders, CURLOPT_URL, $orders_url);
                                    $orders_response = curl_exec($chOrders);
                                    if (curl_errno($chOrders)) {
                                        echo 'Error:' . curl_error($chOrders);
                                    } else {
                                        $orders_data = json_decode($orders_response, true);
                                        // echo $orders_data['documents'] ;
                                        try {
                                            if (isset($orders_data['documents'])) {
                                                $orders_count = count($orders_data['documents']);
                                                $total_orders_count += $orders_count;
                                            }
                                        } catch (error) {
                                        }
                                    }
                                }
                                echo "$total_orders_count";
                            } else {
                                echo 0;
                            }
                        }

                        curl_close($chOrders);
                        ?>
                    </p> <!-- Replace with actual number of orders -->
                </div>
                <!-- <div class="statistic">
                    <i class="fas fa-truck"></i>
                    <h3>Drivers</h3>
                    <p>200</p>   
                </div> -->
            </div>
        </div>
        <div class="card" id="ad">
            <div class="card-header">Ad Management</div>
            <div class="form-card">
                <form id="addForm">
                    <input type="file" name="file" id="imageInput" class="button" placeholder="Select ad slideshow" required>
                    <button class="button" type="button" name="submit" id="add-slid-btn">Add Slideshow
                        Image</button>
                </form>
            </div>
            <div class="slideshow" id="slideshow">
                <?php
                $project_id = 'm2chanic';
                $collection_id = 'ads';

                $url = baseFireStore . "{$project_id}/databases/(default)/documents/{$collection_id}?key={$api_key}";

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                } else {
                    $data = json_decode($response, true);
                    foreach ($data['documents'] as $document) {
                        $fields = $document['fields'];
                        if (isset($fields['imgurl']['stringValue'])) {
                            $imgUrl = $fields['imgurl']['stringValue']; ?>

                            <div class="slide">
                                <img src="<?php echo $imgUrl ?>" alt="Slide">
                                <input type="hidden" class="slide-id" value="<?php echo basename($document['name']); ?>" />
                            </div>
                <?php
                        }
                    }
                }
                curl_close($ch);
                ?>
            </div>
        </div>
        <div class="card" id="workshop">
            <div class="card-header">Automative repair shops</div>
            <button class="button" onclick="openAddWorkshopModal()">Add shop</button>
            <!-- Workshop List -->
            <input type="text" id="myInput" onkeyup="searchTable()" placeholder="Search for names..">
            <table id="myTable">
                <tr>
                    <th onclick="sortTable(0)">Automative repair <span class="arrow-up"></span><span class="arrow-down"></span>
                    </th>
                    <th onclick="sortTable(1)">District<span class="arrow-up"></span><span class="arrow-down"></span>
                    </th>
                    <th onclick="sortTable(2)">Street<span class="arrow-up"></span><span class="arrow-down"></span></th>
                    <th onclick="sortTable(3)">City<span class="arrow-up"></span><span class="arrow-down"></span></th>
                    <th>Details</th>
                    <th>Block</th>
                </tr>

                <?php
                $collection_id = 'ServiceProvider';
                $urlProvider = baseFireStore . "{$project_id}/databases/(default)/documents/{$collection_id}?key={$api_key}";
                $chProvider = curl_init();
                curl_setopt($chProvider, CURLOPT_URL, $urlProvider);
                curl_setopt($chProvider, CURLOPT_RETURNTRANSFER, 1);

                $responseProvider = curl_exec($chProvider);
                if (curl_errno($chProvider)) {
                    echo 'Error:' . curl_error($chProvider);
                } else {
                    $data = json_decode($responseProvider, true);
                    foreach ($data['documents'] as $document) {
                        $fields = $document['fields'];
                ?>
                        <tr class="shop-row">
                            <td><?php echo $fields['name']['stringValue'] ?></td>
                            <td><?php echo $fields['automativeRepair']['mapValue']['fields']['district']['stringValue'] ?></td>
                            <td><?php echo $fields['automativeRepair']['mapValue']['fields']['streetName']['stringValue'] ?> </td>
                            <td><?php echo $fields['automativeRepair']['mapValue']['fields']['city']['stringValue'] ?> </td>

                            <td><i class="far fa-eye eye-icon" onclick="showDetails(new Workshop('<?php echo $fields['automativeRepair']['mapValue']['fields']['district']['stringValue'] ?>', '<?php echo $fields['automativeRepair']['mapValue']['fields']['streetName']['stringValue'] ?>','<?php echo $fields['automativeRepair']['mapValue']['fields']['city']['stringValue'] ?>'),new ServiceProvider('<?php echo basename($document['name']) ?>','<?php echo $fields['name']['stringValue'] ?>','<?php echo $fields['phoneNumber']['stringValue'] ?>','<?php echo $fields['email']['stringValue'] ?>'))"></i>
                            </td>
                            <td class="block-td">
                                <!-- <i class="fas fa-ban" data-workshop="<?php echo basename($document['name']) ?>" onclick="toggleWorkshopStatus('<?php echo $fields['name']['stringValue'] ?>')"></i> -->
                            </td>
                            <td hidden><input class="service-id" value="<?php echo basename($document['name']) ?>"></input></td>

                        </tr>
                <?php
                    }
                }
                curl_close($chProvider);
                ?>
                <!-- Add more rows as needed -->
            </table>
        </div>

    </div>

    <!-- mode to show workshop -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="workshop-details">
                <h2>Automative repair shop Details</h2>
                <p><strong>District:</strong> <span id="district"></span></p>
                <p><strong>Street Name:</strong> <span id="streetName"></span></p>
                <p><strong>City:</strong> <span id="city"></span></p>
                <h2>Service Provider Details</h2>
                <p><strong>ID:</strong> <span id="providerId"></span></p>
                <p><strong>Name:</strong> <span id="providerName"></span></p>
                <p><strong>Phone Number:</strong> <span id="providerPhone"></span></p>
                <p><strong>Email:</strong> <span id="providerEmail"></span></p>
            </div>
        </div>
    </div>


    <div id="addWorkshopModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddWorkshopModal()">&times;</span>
            <div class="workshop-form">
                <h2>Automative repair</h2>
                <form id="workshopForm">
                    <label for="district">District:</label>
                    <input type="text" id="districtInput" name="district" required><br><br>
                    <label for="streetName">Street Name:</label>
                    <input type="text" id="streetNameInput" name="streetName" required><br><br>
                    <label for="city">City:</label>
                    <input type="text" id="cityInput" name="city" required><br><br>
                    <h2>Service Provider</h2>
                    <label for="serviceProviderName">Service Provider Name:</label>
                    <input type="text" id="serviceProviderName" name="serviceProviderName" required><br><br>
                    <label for="phoneNumber">Phone Number:</label>
                    <input type="text" id="phoneNumber" name="phoneNumber" required><br><br>
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" required><br><br>
                    <label for="providerPassword">Password:</label>
                    <input type="password" id="providerPassword" name="providerPassword" required>
                    <button class="add-btn" type="button">Add</button>
                </form>
            </div>
        </div>
    </div>

    <div id="repairShopsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeShopsModal()">&times;</span>
            <h2>Automotive Repair Shops</h2>
            <div id="repairShopsList">
                <?php
                for($i=0;$i<count($drivers_counts);$i++){ ?>
                <h3><i class="fas fa-warehouse" style="color: grey;"></i> <?php echo $service_provider_names[$i] ;?></h3>
                <p><i class="fas fa-users" style="color: #FBCD08FF;margin-left:20px;" ></i> Drivers count: <?php echo $drivers_counts[$i]; ?></p>
                <?php }
                ?>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="assets/js/script.js"></script>
    <script type="text/javascript" src="assets/js/transitions.js" type="text"></script>
    <script type="text/javascript" src="assets/js/table.js" type="text"></script>
    <script>
        function showRepairShops() {
            const modal = document.getElementById('repairShopsModal');
            const repairShopsList = document.getElementById('repairShopsList');

            // Example data structure

            modal.style.display = 'block';
        }

        function closeShopsModal() {
            document.getElementById('repairShopsModal').style.display = 'none';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="module">
        import {
            initializeApp
        } from 'https://www.gstatic.com/firebasejs/10.5.2/firebase-app.js';
        import {
            getFirestore,
            collection,
            getDocs,
            query,
        } from 'https://www.gstatic.com/firebasejs/10.5.2/firebase-firestore.js';
        const firebaseConfig = {
            apiKey: "AIzaSyBjVQ4qjUUO42eqPL_gPkDU_DDuhT9_4ms",
            authDomain: "m2chanic.firebaseapp.com",
            projectId: "m2chanic",
            storageBucket: "m2chanic.appspot.com",
            messagingSenderId: "905844914008",
            appId: "1:905844914008:web:9f027ce3dfc2c142da572d",
            measurementId: "G-5WYG8GY8SS"
        };

        const app = initializeApp(firebaseConfig);
        const db = getFirestore(app);


        document.addEventListener('DOMContentLoaded', async function () {
            const usersQuery = collection(db, 'users');
            const usersSnapshot = await getDocs(usersQuery);

            const monthlyData = [0,0,0,0,0,0,0,0,0,0,0,0];
            const annualData = [0,0,0,0,0,0,0,0,0,0,0,0];
            for (const userDoc of usersSnapshot.docs) {
                const userId = userDoc.id;

                // Retrieve orders for the user
                const ordersQuery = query(collection(db, `users/${userId}/orders`));
                const ordersSnapshot = await getDocs(ordersQuery);

                // Iterate through orders
                for (const orderDoc of ordersSnapshot.docs) {
                    const services = orderDoc.data().services || [];
                    let totalServicesPrice = 0;

                    services.forEach((service) => {
                        const servicePrice = service.price || 0;
                        totalServicesPrice += servicePrice;
                    });

                    const orderDateTime = orderDoc.data().orderDateTime;
                    if (orderDateTime && orderDateTime !== "") {
                        const orderDate = new Date(orderDateTime);
                        const month = orderDate.getMonth()-1;


                        // Ensure monthlyData and annualData are initialized properly
                     //   monthlyData[year] = monthlyData[year] || [];
                        annualData[month] = annualData[month] || 0;

                        // Populate data in arrays
                        monthlyData[month] += totalServicesPrice;

                        annualData[month] += totalServicesPrice;
                    }
                }
            }

            // Display statistics
            const statsContainer = document.getElementById('stats-container');
            statsContainer.innerHTML = `
                <p>Monthly Earnings: $${calculateTotal(monthlyData)}</p>
                <p>Annual Earnings: $${calculateTotal(annualData)}</p>
            `;

            // Create chart
            const ctx = document.getElementById('payment-chart').getContext('2d');
            const paymentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: getMonthLabels(),
                    datasets: [
                        {
                            label: 'Monthly Earnings',
                            borderColor: 'blue',
                            data: Object.values(monthlyData),
                        },
                        {
                            label: 'Annual Earnings',
                            borderColor: 'green',
                            data: Object.values(annualData),
                        },
                    ],
                },
            });
        });

        // Function to calculate total earnings
        function calculateTotal(data) {
            return data.flat().reduce((total, amount) => total + amount, 0);
        }

        // Function to get month labels
        function getMonthLabels() {
            return [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ];
        }
    </script>
</body>

</html>