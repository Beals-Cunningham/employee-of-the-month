<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chroma-js/2.4.2/chroma.min.js" integrity="sha512-zInFF17qBFVvvvFpIfeBzo7Tj7+rQxLeTJDmbxjBz5/zIr89YVbTNelNhdTT+/DCrxoVzBeUPVFJsczKbB7sew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>

<?php
$file = file_get_contents('.env');
$lines = explode("\n", $file);

$servername = "";
$username = "";
$password = "";
$dbname = "";

foreach ($lines as $line) {
    if (strpos($line, "SERVERNAME") !== false) {
        $servername = trim(str_replace("SERVERNAME=", "", $line));
    } elseif (strpos($line, "USERNAME") !== false) {
        $username = trim(str_replace("USERNAME=", "", $line));
    } elseif (strpos($line, "PASSWORD") !== false) {
        $password = trim(str_replace("PASSWORD=", "", $line));
    } elseif (strpos($line, "DBNAME") !== false) {
        $dbname = trim(str_replace("DBNAME=", "", $line));
    }
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$ipAddress = $_SERVER['REMOTE_ADDR'];
$thisMonth = date('m');

$sql = "SELECT * FROM votes WHERE vote_from_ip = '$ipAddress' AND vote_month = '$thisMonth'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $hasVotedFromThisIpThisMonth = true;
}

        

        $timeLeft = getTimeLeftInMonth();

        function getTimeLeftInMonth()
        {
            $now = new DateTime();
            $endOfMonth = new DateTime('last day of this month');
            $endOfMonth->setTime(12, 0, 0);
            $interval = $now->diff($endOfMonth);

            $days = $interval->format('%a');
            $hours = $interval->format('%h');

            return [
                'days' => $days,
                'hours' => $hours,
            ];
        }

        $hasVotedFromThisIpThisMonth = false;

        if ($hasVotedFromThisIpThisMonth) {
            echo '<h2>You have already voted this month.</h2>';
            exit;
        } else if ($timeLeft['days'] < 1 && $timeLeft['hours'] < 1) {
            echo '<h2>Voting is closed for this month.</h2>';
            exit;
        } else {
            echo '
            <div>
        <h1>Vote for Employee of the Month</h1>
        <form>
            <div style = "display:flex;gap:4px;">
            <label for="employee" style = "margin-top:auto;margin-bottom:auto;">Vote for</label>
            <select name="employee" id="employee" required>
            </select>
            <label for="employee-from" style = "margin-top:auto;margin-bottom:auto;">Your name</label>
            <select name="employee-from" id="employee-from" required>
            </select>
            <input type="submit" value="Submit">
            </div>
            
        </form>
        </div >
        <div id = "results-div" style = "display:none">
        <h2>Current results</h2>
        <h3 id = "winner-text">In the lead: <span id = "winner"></span></h3>
        <div class="chart-container" style="position: relative; max-height:50vh;">
            <canvas id="chart"></canvas>
        </div>
        <div>

        </div>
        </div>
        <h3>Voting closes in: ' . $timeLeft['days'] . ' days, ' . $timeLeft['hours'] . ' hours'
                . '</h3>
                <small id = "loginsmall">Log in to see results: <input id = "password"><button id = "submit-password">Log in</button></small></div>
        <div>
        ';
        }
        ?>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
        const ctx = document.getElementById('chart');
        const labels = [
            'Sayer',
            'Abbie',
            'Amelia',
            'Ashley',
            'Avery',
            'Claire',
            'Don',
            'Jamie',
            'Joseph',
            'KaCee',
            'Karsten',
            'Kellen',
    'Kelli',
    'Kelsi',
    'Kris',
    'Maddy D.',
    'Madison Z.',
    'Makk',
    'Phil',
    'Mary',
    'Matt',
    'Nick',
    'Paul',
    'Sharon',
    'Tiffani M.',
    'Tomasz',
    'Wes',
    'Yvone',
    'Zak'
  ];
  let pie = new Chart(ctx, {
    type: 'pie',
    data: {
  labels: labels,
  datasets: [{
    label: '',
    data: [],
    backgroundColor: [
    ],
    hoverOffset: 4
  }]
},
    options: {
      scales: {
        y: {
          display: false
        }
      }
    }
  });
</script>

<script>
    populateForm()
    <?php
            $file = file_get_contents('.env');
            $lines = explode("\n", $file);
            foreach ($lines as $line) {
                $parts = explode('=', $line);
                if ($parts[0] === 'EMPLOYEE_OF_THE_MONTH_PASSWORD') {
                    $password = $parts[1];
                }
            }
            echo 'const correct_password = "'.$password.'";';
        ?>
    function showResults(){

        let password = document.getElementById('password').value
        
        if (password === correct_password){
            document.getElementById('results-div').style.display = 'block'
            document.getElementById('password').style.display = 'none'
            document.getElementById('submit-password').style.display = 'none'
            document.getElementById('loginsmall').style.display = 'none'
            initChart(correct_password).then(() => {
                populateForm()
                pie.update()
                getWinner()
            })

        }
    }

    let submitPassword = document.getElementById('submit-password')
    submitPassword.addEventListener('click', function(e){
        e.preventDefault()
        let winnerText = document.getElementById('winner-text')
        winnerText.innerText = 'Loading...'
        showResults()
    })


    function populateForm(){
        let select = document.getElementById('employee')
        let select_from = document.getElementById('employee-from')

        for (let i = 0; i < labels.length; i++) {
            let option = document.createElement('option')
            option.value = labels[i]
            option.text = labels[i]
            select.appendChild(option)
            select_from.appendChild(option.cloneNode(true))
        }
    }

    async function initChart(p){
        let winnerText = document.getElementById('winner-text')
        winnerText.innerText = 'Loading...'
        let labels = pie.data.labels
        let data = pie.data.datasets[0].data
        let backgroundColor = pie.data.datasets[0].backgroundColor
        let deltae = 0
        let color1 = chroma.random()
        let color2 = chroma.random()
        while (deltae < 40) {
            color2 = chroma.random()
            deltae = chroma.deltaE(color1, color2)
        }
        
        let stops = chroma.scale([color1, color2]).mode('lch').colors(labels.length)

        tempData = await fetch(
            'get-votes.php',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    password: p
                })
            }
        ).then(response => response.json()).then(d => {
            console.log(d)
            return d
        }).catch(err => {
            console.log(err)
        })

        

        for (let i = 0; i < labels.length; i++) {
            winnerText.innerText = 'Loading...'
            data[i] = tempData[labels[i]]

            backgroundColor[i] = stops[i]
        }
        
    }

    function getWinner(){
        let labels = pie.data.labels
        let data = pie.data.datasets[0].data
        let max = 0
        let winner = ''

        for (let i = 0; i < data.length; i++) {
            if (data[i] > max) {
                max = data[i]
                winner = labels[i]
            }
        }
        if (winner){
            winner = winner + ' with ' + max + ' votes!'
        } else {
            winner = 'No votes yet!'
        }
        let winnerText = document.getElementById('winner-text')
        winnerText.innerText = 'In the lead: ' + winner

    }

    async function submitVote(){
        let select = document.getElementById('employee')
        let employee = select.value
        let select_from = document.getElementById('employee-from')
        let employee_from = select_from.value
        let labels = pie.data.labels
        let data = pie.data.datasets[0].data
        let index = labels.indexOf(employee)
        data[index]++
        pie.update()
        getWinner()
        let submit = document.querySelector('input[type="submit"]')
        submit.disabled = true
        select.disabled = true
        let r = await fetch('submit-vote.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                vote_for: employee,
                vote_from: employee_from
            })
        }).catch(err => {
            console.log(err)
        }).then((response) => {
            console.log(response)
            return response.text()
        })
        console.log(r)
            if (r === 'You have already submitted a vote this month.'){
                let form = document.querySelector('form')
                form.innerHTML = '<h2>You have already submitted a vote this month.</h2>'
                
            } else {
                let form = document.querySelector('form')
                form.innerHTML = '<h2>Vote submitted successfully</h2>'
            }
    }

    let form = document.querySelector('form')
    form.addEventListener('submit', function(e){
        e.preventDefault()
        submitVote()
    })

</script>

</body>
</html>
