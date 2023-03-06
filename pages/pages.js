function selsize() {
    var sel=document.getElementById('selsize').selectedIndex;
    location.href = './contentsize.php?selsize='+ sel;
    console.log(sel);
}