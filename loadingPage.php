<script>
        window.onload = function(){ document.getElementById("loading").style.display = "none" }
        setTimeout(() => {
        }, 2000);
    </script>
    <style>
        #loading {width: 100%;height: 100%;top: 0px;left: 0px;position: fixed;display: block; z-index: 99}
        #loading-image {position: absolute;top: 40%;left: 45%;z-index: 100} 
    </style>
    <div id="loading">
        <img id="loading-image" src="img/Double Ring-1.1s-197px.gif" width="120px" alt="Loading..." />
</div>