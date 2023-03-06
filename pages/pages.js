function selsize() {
    let sel=document.getElementById('selsize').selectedIndex;
    location.href = './pages/contentsize.php?selsize='+ sel;
    console.log(sel);
}