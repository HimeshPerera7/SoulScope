function loadCities(districtId) {
        let district = parseInt(districtId);
        $.ajax({
            url: "api/city.php?id="+district,
            type: "GET",
            dataType: "json",
            success: function (data) {
                let $select = $("#city");
                let defaultOption = $select.find("option:first").detach();
                $select.empty().append(defaultOption);
                $.each(data.data, function (index, city) {
                    $select.append($("<option>", {
                        value: city.id,
                        text: city.name_en
                    }));
                });
            },
            error: function () {
                console.error("Error fetching cities data");
            }
        });
}


