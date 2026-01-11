jQuery(document).ready(function($){

    var dt = $('#eps_transections_show').DataTable({    
        ajax: {
            url: datatablesajax.transection_url+"?action=eps_transection_endpoint",
            cache:false,
        },
        columns: [
            { data: 'id' },        
            { data: 'customer_account' },  
            { data: 'order_id' },
            { data: 'response_description' },
            { data: 'amount' },
            { data: 'transection_id' },
            
            { data: 'created_at' },
            {
            data: 'product_status',
                render: function (data, type, row) {
                    var disabled = data === 'Completed' ? 'disabled' : '';
                    var canceled = data === 'Canceled' ? 'disabled' : '';
                    return `
                        <select class="status-dropdown" data-id="${row.id}" ${disabled} ${canceled}>
                            <option value="Pending" ${data === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="In Progress" ${data === 'In Progress' ? 'selected' : ''}>In Progress</option>
                            <option value="Completed" ${data === 'Completed' ? 'selected' : ''}>Completed</option>
                            <option value="Canceled" ${data === 'Canceled' ? 'selected' : ''}>Canceled</option>
                        </select>
                    `;
                }
            },
           
        ],
        pageLength: 25,
        "order": [[ 6, "desc" ]],
            initComplete: function () {
        var defaultStatus = $('#responseFilter').val(); // "Success"
        dt.column(3).search(defaultStatus).draw();
    }


        
    
    }); //.DataTable()
$('#responseFilter').on('change', function () {
    let value = $(this).val();

    dt.column(3) // response_description index
      .search(value ? '^' + value + '$' : '', true, false)
      .draw();
});

     // 🔄 STATUS UPDATE AJAX
   jQuery(document).ready(function($){
    $('#eps_transections_show').on('change', '.status-dropdown', function(){
        var status = $(this).val();
        var id = $(this).data('id');
        var $dropdown = $(this);
        $.ajax({
            url: eps_ajax.ajax_url, // use localized ajax_url
            type: 'POST',
            data: {
                action: 'eps_update_product_status',
                id: id,
                status: status,
                _ajax_nonce: eps_ajax.nonce
            },
            success: function(response){
                if(response.success){
                    $('<div class="eps-success-msg">Status updated successfully!</div>')
                        .appendTo('body')
                        .fadeIn(200)
                        .delay(1500)
                        .fadeOut(500, function() { $(this).remove(); });
                    
                    if (status === 'Completed' || status === 'Canceled') {
                        $dropdown.prop('disabled', true);
                    }
                    
                } else {
                    
                    console.error(response.data.message);
                }
            },
            error: function(xhr){
                console.error('AJAX error:', xhr.responseText);
            }
        });
    });
});
jQuery(document).ready(function($){
    
   $('#eps-sync-12, #eps-sync-7').on('click', function () {

        var $btn = $(this);
        var range = $btn.data('range');

        var originalText = $btn.text();
        $btn.prop('disabled', true).text('Syncing...');

        $.ajax({
            url: eps_ajax.ajax_url,
            type: 'POST', 
            data: {
                action: 'eps_sync_gateway', // custom AJAX action
                range: range,
                _ajax_nonce: eps_ajax.nonce
            },
            success: function(response){
                if(response.success){
                    $('<div class="eps-success-msg">Data synced successfully!</div>')
                        .appendTo('body')
                        .fadeIn(200)
                        .delay(1500)
                        .fadeOut(500, function() { $(this).remove(); });
                    
                    // Optionally, reload DataTable
                    //$('#eps_transections_show').DataTable().ajax.reload();
                    $('#eps_transections_show').DataTable().ajax.reload(null, false);
                } else {
                    
                     $('<div class="eps-error-msg">Failed to fetch data from gateway</div>')
                        .appendTo('body')
                        .fadeIn(200)
                        .delay(1500)
                        .fadeOut(500, function() { $(this).remove(); });
                }
                $btn.prop('disabled', false).text(originalText);
            },
            error: function(xhr){
                 $('<div class="eps-error-msg">Failed to fetch data from gateway</div>')
                        .appendTo('body')
                        .fadeIn(200)
                        .delay(1500)
                        .fadeOut(500, function() { $(this).remove(); });
                $btn.prop('disabled', false).text(originalText);
            }
        });
    });
});


});
