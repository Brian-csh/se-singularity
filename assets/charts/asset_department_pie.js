department_labels = Object.keys(department_counts);
department_data = Object.values(department_counts);
// department_labels = ["IT", "HR", "Finance", "Marketing", "Sales", "Operations", "Legal", "R&D", "Customer Service", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10"];
// department_data = Array.from(Array(department_labels.length).keys());
// Pie chart displaying the distribution of assets by status
var ctx = document.getElementById("assetDepartmentPieChart");
var assetDepartmentPieChart = new Chart(ctx, {
    type: "doughnut",
    data: {
        labels: department_labels,
        datasets: [{
            data: department_data,          
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
