<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <style>
        .mapControls {
            display: grid;
            grid-template-columns: 100px auto;
            gap: 10px;
        }

        .layoutContainer {
            display: grid;
            grid-template-columns: 350px auto;
            gap: 10px;
        }

        .layoutContainer div:nth-child(2) {
            border: 1px solid black;
        }

        .solution {
            font-size: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="layoutContainer">
        <div class="menu">
            <p class="mapControls">
                <label for="mapSelect">Select Map:</label>
                <select id="mapSelect">
                    <?php
                    $maps = glob("../data/task3/*.json");
                    echo "<option value=''>Please select</option>";
                    foreach ($maps as $map) {
                        $map = basename($map);
                        echo "<option value='$map'>$map</option>";
                    }   ?>
                </select>
            </p>

            <p class="mapControls">
                <label for="mapRows">Rows:</label>
                <input
                    type="number"
                    min="2"
                    max="30"
                    id="mapRows"
                    value="2" />
                <label for="mapCols">Cols:</label>
                <input
                    type="number"
                    min="2"
                    max="30"
                    id="mapCols"
                    value="2" />
                <button id="saveMap">Save</button>
                <button id="createMap">Create Map</button>
            </p>
            <p class="mapControls">
                <label for="passWalNumber">Walls that can pass through:</label>
                <input
                    type="number"
                    min="0"
                    max="10"
                    value="1"
                    id="passWalNumber" />
                <label for="animateSolution"><input type="checkbox" id="animateSolution" />
                    Animate</label>
                <input
                    type="range"
                    id="animationSpeed"
                    min="0"
                    max="300"
                    value="50" />
            </p>
            <p class="mapControls">

                <label><input type="checkbox" id="withLabels" /> Labels</label>

                <button id="solveMaze">Solve Maze</button>
            </p>
            <p class="solution">
                <span>Path Length: </span>
                <b id="shortestPathLength"></b>
            </p>
            <p class="solution">
                <span>Iterrations: </span>
                <b id="itterations"></b>
            </p>

        </div>
        <div id="mapContainer"></div>
    </div>
</body>
<footer>
    <script>
        let map = [
            []
        ];


        document.querySelector("#createMap").addEventListener("click", () => {
            const rows = parseInt(document.querySelector("#mapRows").value);
            const cols = parseInt(document.querySelector("#mapCols").value);
            document.querySelector("#mapSelect").value = '';
            createMap(rows, cols);
            renderMap();
        });

        document.querySelector("#solveMaze").addEventListener("click", solveMaze);


        document.querySelector("#mapSelect").addEventListener("change", (e) => {
            if (!e.target.value) return;
            fetch(`server.php?action=getMap&map=${e.target.value}`)
                .then((res) => res.json())
                .then((data) => {
                    loadRawMap(data);
                    renderMap();
                });
        });

        document
            .querySelector("#saveMap")
            .addEventListener("click", () => {
                if (map.length == 1 && map[0].length == 0) return;
                const mapName = prompt("Enter map name");
                if (!mapName) return;
                const mapJson = JSON.stringify(getRawMap());
                fetch("server.php", {
                    method: "POST",
                    body: new URLSearchParams({
                        action: "saveMap",
                        mapName,
                        map: mapJson
                    })
                }).then((res) => res.json()).then((data) => {
                    if (data.status == "success") {
                        alert("Map saved successfully");
                        document.querySelector("#mapSelect").innerHTML += `<option value="${mapName}.json">${mapName}.json</option>`;
                        document.querySelector("#mapSelect").value = `${mapName}.json`;
                    }
                }).catch((err) => {
                    console.error(err);
                });
            });

        function solveMaze() {
            if (map.length == 1 && map[0].length == 0) {
                alert("Map is empty");
                return;
            }

            document.querySelectorAll("input, button, select").forEach((input) => {
                input.disabled = true;
            });

            const passWalNumber = parseInt(document.querySelector("#passWalNumber").value);
            const animate = document.querySelector("#animateSolution").checked;
            const animationSpeed = parseInt(document.querySelector("#animationSpeed").value);

            fetch("server.php", {
                    method: "POST",
                    body: new URLSearchParams({
                        action: "solveMaze",
                        map: JSON.stringify(getRawMap()),
                        passWalNumber
                    })
                })
                .then((res) => res.json())
                .then(async (data) => {
                    map = data.map;
                    renderMap();


                    if (!data.hasSolution) {
                        alert("No path found");
                        return;
                    }

                    if (animate) {
                        await animateSolution(data.itterations, animationSpeed);
                    }
                    drawShortestPath(data.solution);
                    document.querySelector("#itterations").innerText = data.itterations.length;
                    document.querySelector("#shortestPathLength").innerText = data.solution.length;
                }).finally(() => {
                    document.querySelectorAll("input, button, select").forEach((input) => {
                        input.disabled = false;
                    });
                });
        }

        function animateSolution(itterations, speed) {
            return new Promise((resolve) => {
                let i = 0;
                document.querySelectorAll(`td span`).forEach((span) => {
                    span.innerText = "";
                });
                const interval = setInterval(() => {
                    const cell = itterations[i];
                    const cellElement = document.querySelector(`#c-${cell.row}-${cell.col}`);
                    cellElement.style.backgroundColor = "lightblue";
                    const span = cellElement.querySelector("span");
                    if (span) span.innerText = cell?.paths?.join("/");

                    document.querySelector("#itterations").innerText = i + 1;

                    setTimeout(() => {
                        cellElement.style.backgroundColor = cell.isWall ? "gray" : "white"
                    }, speed);

                    i++;
                    if (i >= itterations.length) {
                        clearInterval(interval);
                        setTimeout(() => {
                            resolve();
                        }, speed + 10);
                    }
                }, speed);
            });
        }

        function drawShortestPath(solution) {
            if (!solution instanceof Array) return;
            solution.forEach((cell) => {
                const cellElement = document.querySelector(`#c-${cell[0]}-${cell[1]}`);
                cellElement.style.backgroundColor = "lightgreen";
            });
        }

        function getRawMap() {
            return map.map((row) => row.map((cell) => cell.isWall ? 1 : 0));
        }

        function loadRawMap(rawMap) {
            map = [];
            if (!(rawMap instanceof Array)) {
                alert("Invalid map");
                return;
            }
            rawMap.forEach((row, r) => {
                if (!(row instanceof Array)) {
                    alert("Invalid map");
                    return;
                }
                map[r] = [];
                row.forEach((cell, c) => {
                    map[r][c] = {
                        row: r,
                        col: c,
                        isWall: cell,
                        paths: []
                    };
                });
            });
        }


        function createMap(rows, cols) {
            map = [];
            for (let row = 0; row < rows; row++) {
                map.push([]);
                for (let col = 0; col < cols; col++) {
                    map[row][col] = {
                        row,
                        col,
                        isWall: 0,
                        paths: []
                    };
                }
            }
            console.log(map);
        }

        function renderMap() {
            const withLabels = document.querySelector("#withLabels").checked;
            const mapContainer = document.querySelector("#mapContainer");
            mapContainer.innerHTML = "";

            const table = document.createElement("table");
            mapContainer.appendChild(table);
            table.border = "1";
            map.forEach((row, r) => {
                const rowElement = document.createElement("tr");
                row.forEach((cell, c) => {
                    const cellElement = document.createElement("td");
                    cellElement.id = `c-${r}-${c}`;
                    rowElement.appendChild(cellElement);
                    cellElement.style.backgroundColor = cell.isWall ? "gray" : "white";

                    if (withLabels) {
                        const span = document.createElement("span");
                        span.style.fontSize = "9px";
                        span.innerText = cell?.paths?.join("/");
                        cellElement.appendChild(span);
                    }

                    if (r == 0 && c == 0) return;
                    if (r == map.length - 1 && c == map[0].length - 1) return;
                    const cb = document.createElement("input");
                    cb.type = "checkbox";
                    cb.id = `cb-${r}-${c}`;
                    cb.checked = cell.isWall;
                    cb.onclick = function(e) {
                        if (e.target.checked) {
                            cellElement.style.backgroundColor = "gray";
                            cell.isWall = true;
                        } else {
                            cellElement.style.backgroundColor = "white";
                            cell.isWall = false;
                        }
                    };
                    cellElement.appendChild(cb);
                });
                table.appendChild(rowElement);
            });
        }
    </script>

</footer>

</html>