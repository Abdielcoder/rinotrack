<?php
// Archivo de prueba para verificar sintaxis JavaScript en PHP

$testJS = '<script>
function testFunction() {
    const element = document.querySelector(`button[onclick=\"test(123)\"]`);
    element.innerHTML = \'<i class="fas fa-test"></i>\';
    console.log("Test completed");
}
</script>';

echo $testJS;
echo "\nSintaxis correcta!";
?>
