//----- Search
$(document).ready(function() {
    $("#navbar-search-input").keyup(function(e) {
        let keyword = $(this).val();

        if (keyword) {
            $.ajax({
                type: "POST",
                url: '/search',
                data: {
                    keyword: keyword
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(response) {
                    const data = response.result;
                    let html = '';
                    data.forEach(element => {
                        html += `<a class="text-secondary" href="">
                                    <li class="list-group-item">${element.customer}</li>
                                </a>`
                    });

                    $("#search-list").html(html);
                },
                error: function(event) {
                    $("#search-list").html(
                        '<li class="list-group-item">Data tidak ditemukan</li>');
                }
            });
        } else {
            $("#search-list").html('');
        }
    });
});