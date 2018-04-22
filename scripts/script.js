function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split('&')
        .forEach(function (item) {
            tmp = item.split('=');
            if (tmp[0] === parameterName) {
                result = decodeURIComponent(tmp[1]);
            }
        });
    return result;
}

let title = findGetParameter('title');
let content = findGetParameter('content');
if (title !== null && content !== null) {
    $('#modal').modal();
}

let tab = findGetParameter('tab');
if (tab !== null) {
    if (tab === 'person') {
        $('#nav-tab a[href="#nav-person"]').tab('show');
    } else if (tab === 'company') {
        $('#nav-tab a[href="#nav-company"]').tab('show');
    }
}