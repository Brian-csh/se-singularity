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
            backgroundColor: [
                "rgba(0, 97, 242, 1)",
                "rgba(0, 172, 105, 1)",
                "rgba(88, 0, 232, 1)",
                "rgba(255, 159, 64, 1)",
                "rgba(255, 99, 132, 1)",
                "rgba(54, 162, 235, 1)",
                "rgba(255, 206, 86, 1)",
                "rgba(75, 192, 192, 1)",
                "rgba(153, 102, 255, 1)",
                "rgba(255, 159, 64, 1)"
              ],
              hoverBackgroundColor: [
                "rgba(0, 97, 242, 0.9)",
                "rgba(0, 172, 105, 0.9)",
                "rgba(88, 0, 232, 0.9)",
                "rgba(255, 159, 64, 0.9)",
                "rgba(255, 99, 132, 0.9)",
                "rgba(54, 162, 235, 0.9)",
                "rgba(255, 206, 86, 0.9)",
                "rgba(75, 192, 192, 0.9)",
                "rgba(153, 102, 255, 0.9)",
                "rgba(255, 159, 64, 0.9)"
              ],              
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
        cutoutPercentage: 80
    }
});
