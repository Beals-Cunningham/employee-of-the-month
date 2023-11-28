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
    //TODO: get data from database
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $thisMonth = date('m');
        $timeLeft = getTimeLeftInMonth();

        function getTimeLeftInMonth() {
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
        } else if($timeLeft['days'] < 1 && $timeLeft['hours'] < 1) {
            echo '<h2>Voting is closed for this month.</h2>';
            exit;
        } else {
            echo '
            <div>
    <h1>Vote for Employee of the Month</h1>
    <form>
        <select name="employee" id="employee">
            <option value="0">Select an Employee</option>
        </select>
        <input type="submit" value="Submit">
    </form>
</div >
<div id = "results-div" style = "display:none">
<h2>Current results</h2>
<h3>In the lead: <span id = "winner"></span></h3>
    <div class="chart-container" style="position: relative; max-height:50vh;">
        <canvas id="chart"></canvas>
    </div>
    <div>
            
    </div>
</div>
<h3>Voting closes in: '.$timeLeft['days'].' days, '.$timeLeft['hours'].' hours'
            .'</h3>
            <small>Log in to see results: <input id = "password"><button id = "submit-password">Log in</button></small></div>
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
    function showResults(){
        let password = document.getElementById('password').value
        
        //read the correct password from employee-of-the-month.env
        <?php
            $file = file_get_contents('.env');
            $lines = explode("\n", $file);
            foreach ($lines as $line) {
                $parts = explode('=', $line);
                if ($parts[0] === 'EMPLOYEE_OF_THE_MONTH_PASSWORD') {
                    $password = $parts[1];
                }
            }
            echo 'let correct_password = "'.$password.'";';
        ?>
        if (password === correct_password){
            document.getElementById('results-div').style.display = 'block'
            document.getElementById('password').style.display = 'none'
            document.getElementById('submit-password').style.display = 'none'
            initChart()
            populateForm()
            getWinner()
        }
    }


    function populateForm(){
        let select = document.getElementById('employee')

        for (let i = 0; i < labels.length; i++) {
            let option = document.createElement('option')
            option.value = labels[i]
            option.text = labels[i]
            select.appendChild(option)
        }
    }

    function initChart(){
        let labels = pie.data.labels
        let data = pie.data.datasets[0].data
        let backgroundColor = pie.data.datasets[0].backgroundColor
        let color1 = chroma.random()
        let color2 = chroma.random()
        let stops = chroma.scale([color1, color2]).mode('lch').colors(labels.length)
        

        for (let i = 0; i < labels.length; i++) {
            //TODO: get data from database
            data[i] = 0
            backgroundColor[i] = stops[i]
        }
        pie.update()
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
        let winnerElement = document.getElementById('winner')
        if (winner){
            winner = winner + ' with ' + max + ' votes!'
        } else {
            winner = 'No votes yet!'
        }
        winnerElement.innerText = winner

    }

    function submitVote(){
        let select = document.getElementById('employee')
        let employee = select.value
        let labels = pie.data.labels
        let data = pie.data.datasets[0].data
        let index = labels.indexOf(employee)
        data[index]++
        pie.update()
        getWinner()
        let submit = document.querySelector('input[type="submit"]')
        submit.disabled = true
        select.disabled = true
        //TODO: send data to database
    }

    let form = document.querySelector('form')
    form.addEventListener('submit', function(e){
        e.preventDefault()
        submitVote()
    })

</script>

</body>
</html>
