window.addEventListener('DOMContentLoaded', event => {
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki

    const datatablesSimple = document.getElementById('datatablesSimple');
    if (datatablesSimple) {
        let dataTable = new simpleDatatables.DataTable(datatablesSimple, {
            'perPage': 25,
            'perPageSelect': [25, 50, 100]
        });
        document.getElementById('datatablesSimple').style.display = 'table';
        document.getElementById('tablePreloader').style.display = 'none';
        console.log('loaded');
    }
});
