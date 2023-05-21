// example data
// var status_labels = ["IDLE", "IN USE", "RETIRED", "DELETED", "REPAIRED"];
// var status_data = [3, 2, 1, 4, 9];

status_labels = Object.keys(status_counts);
status_data = Object.values(status_counts);
// Pie chart displaying the distribution of assets by status
var ctx = document.getElementById("assetStatusPieChart");
var assetStatusPieChart = new Chart(ctx, {
    type: "doughnut",
    data: {
        labels: status_labels,
        datasets: [{
            data: status_data,    
            hoverBorderColor: "rgba(234, 236, 244, 1)"
        }]
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: "#dddfeb",
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10
        },
        legend: {
            display: true,
            position: 'right',
            labels: {
                fontColor: 'white',
            }
        },
        plugins: {
            colorschemes: {
                scheme: 'brewer.PRGn11'
                }
        },
        cutoutPercentage: 80
    }
});
