/** @type MazeNode[][] */
let map = [[]];
let MaxNumber = Number.MAX_SAFE_INTEGER;
let wallsNumber = 0;
loadMapFromLocalStorage();

document.querySelector("#mapSelect").addEventListener("change", (e) => {
  if (!e.target.value) return;
  map = JSON.parse(localStorage.getItem("maps"))[e.target.value] || [];
  renderMap();
});

document
  .querySelector("#saveMap")
  .addEventListener("click", saveMapToLocalStorage);

document.querySelector("#createMap").addEventListener("click", () => {
  const rows = parseInt(document.querySelector("#mapRows").value);
  const cols = parseInt(document.querySelector("#mapCols").value);
  document.querySelector("#mapSelect").value = null;
  createMap(rows, cols);
  renderMap();
});

document.querySelector("#solveMaze").addEventListener("click", solveMaze);

/**
 * @param {number} row
 * @param {number} col
 */
function MazeNode(row, col) {
  /** @type NodePath[] */
  this.paths = [];

  /** @type boolean */
  this.isWall = false;

  /** @type number */
  this.row = row;

  /** @type number */
  this.col = col;
}

/**
 * @param {number} length
 * @param {?MazeNode} parent
 */
function NodePath() {
  /** @type number */
  this.length = MaxNumber;

  //   /** @type {?MazeNode} */
  //   this.parent = parent;

  /** @type MazeNode[] */
  this.nodes = [];
}

function solveMaze() {
  if (!map.length || !map[0].length) {
    alert("Please create a map first");
    return;
  }
  wallsNumber = document.querySelector("#passWalNumber").value;
  initializePaths();
  exploreMaze();
  renderMap();
  drawShortestPath();
}
function initializePaths() {
  map.forEach((row) => {
    row.forEach((cell) => {
      cell.paths = [];
      for (let i = 0; i <= wallsNumber; i++) {
        cell.paths.push(new NodePath());
      }
    });
  });
}
function drawShortestPath() {
  let node = map[map.length - 1][map[0].length - 1];
  let shortestPathIndex = 0;
  let shortestPathLength = MaxNumber;
  node.paths.forEach((path, i) => {
    if (path.length < shortestPathLength) {
      shortestPathIndex = i;
      shortestPathLength = path.length;
    }
  });

  if (shortestPathLength >= MaxNumber) {
    alert("No path found");
    return;
  }

  colorNode = function (node) {
    const [r, c] = [node.row, node.col];
    const cellElement = document.querySelector(`#c-${r}-${c}`);
    cellElement.style.backgroundColor = "lightgreen";
  };

  colorNode(node);
  node.paths[shortestPathIndex].nodes.forEach(colorNode);

  document.querySelector("#shortestPathLength").innerText = shortestPathLength;
}

function exploreMaze() {
  const queue = [];
  const root = map[0][0];
  root.paths.forEach((path) => {
    path.length = 1;
    path.nodes = [];
  });
  queue.push(root);
  let itterations = 0;
  while (queue.length) {
    itterations++;
    const node = queue.shift();
    const neighbors = getNeighbors(node);

    neighbors.forEach((neighbor) => {

      if (neighbor.isWall) {
        neighbor.paths.forEach((path, i) => {
          if (i === 0) {
            path.nodes = null;
            return;
          }
          const newPathLength = node.paths[i - 1].length + 1;
          if (path.length > newPathLength) {
            path.length = newPathLength;
            // path.parent = node.paths[i - 1].parent;
            path.nodes = [...node.paths[i - 1].nodes, node];
            queue.push(neighbor);
          }
        });
      } else {
        neighbor.paths.forEach((path, i) => {
          const newPathLength = node.paths[i].length + 1;
          if (path.length > newPathLength) {
            path.length = newPathLength;
            // path.parent = node;
            path.nodes = [...node.paths[i].nodes, node];
            queue.push(neighbor);
          }
        });
      }
    });
    document.querySelector("#itterations").innerText = itterations;
  }
}
function getNeighbors(node) {
  const neighbors = [];
  const [r, c] = [node.row, node.col];
  if (map[r]?.[c + 1]) neighbors.push(map[r][c + 1]);
  if (map[r + 1]?.[c]) neighbors.push(map[r + 1][c]);
  if (map[r]?.[c - 1]) neighbors.push(map[r][c - 1]);
  if (map[r - 1]?.[c]) neighbors.push(map[r - 1][c]);
  return neighbors;
}

function saveMapToLocalStorage() {
  if (!map.length) {
    alert("Please create a map first");
    return;
  }
  let mapName = prompt("Enter map name");
  if (!mapName) return;
  const maps = JSON.parse(localStorage.getItem("maps") || "{}");
  maps[mapName] = map;
  localStorage.setItem("maps", JSON.stringify(maps));
  alert("Map saved successfully");
  loadMapFromLocalStorage();
}
function loadMapFromLocalStorage() {
  const maps = JSON.parse(localStorage.getItem("maps") || "{}");
  const select = document.querySelector("#mapSelect");
  select.innerHTML = "";
  const option = document.createElement("option");
  option.innerText = "Select a map";
  option.value = null;
  select.appendChild(option);
  for (let mapName in maps) {
    const option = document.createElement("option");
    option.value = mapName;
    option.innerText = mapName;
    select.appendChild(option);
  }
}

function createMap(rows, cols) {
  map = [];
  for (let row = 0; row < rows; row++) {
    map.push([]);
    for (let col = 0; col < cols; col++) {
      map[row].push(new MazeNode(row, col));
    }
  }
  MaxNumber = rows * cols * 10;
  console.log(map);
}

function renderMap() {
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

      const span = document.createElement("span");
      span.style.fontSize = "8px";
      const lengths = cell?.paths?.map((path) =>
        path.length >= MaxNumber ? "x" : path.length
      );
      span.innerText = lengths?.join("/");

      cellElement.appendChild(span);

      if (r == 0 && c == 0) return;
      if (r == map.length - 1 && c == map[0].length - 1) return;
      const cb = document.createElement("input");
      cb.type = "checkbox";
      cb.checked = cell.isWall;
      cb.onclick = function () {
        if (cb.checked) {
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
