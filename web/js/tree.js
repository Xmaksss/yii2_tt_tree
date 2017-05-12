var data = [
    { id: 1, level: 1, left_key: 1, right_key: 32 },
    { id: 2, level: 2, left_key: 2, right_key: 9 },
    { id: 3, level: 2, left_key: 10, right_key: 23 },
    { id: 4, level: 2, left_key: 24, right_key: 31 },
    { id: 5, level: 3, left_key: 3, right_key: 8 },
    { id: 6, level: 3, left_key: 11, right_key: 12 },
    { id: 7, level: 3, left_key: 13, right_key: 20 },
    { id: 8, level: 3, left_key: 21, right_key: 2 },
    { id: 9, level: 3, left_key: 25, right_key: 30 },
    { id: 10, level: 4, left_key: 4, right_key: 5 },
    { id: 11, level: 4, left_key: 6, right_key: 7 },
    { id: 12, level: 4, left_key: 14, right_key: 15 },
    { id: 13, level: 4, left_key: 16, right_key: 17 },
    { id: 14, level: 4, left_key: 18, right_key: 19 },
    { id: 15, level: 4, left_key: 26, right_key: 27 },
    { id: 16, level: 4, left_key: 28, right_key: 29 }
];

var Tree = {
    svg: undefined,
    data: [],
    corrLevel: 0,
    init: function(parentID) {
        var self = this;

        $.get('/get-tree/' + parentID, function(data) {
            //console.log(data);
            self.data = data.items;
            self.corrLevel = data.level-1;
            self.run();
        });

    },
    run: function () {
        var self = this;
        this.svg = document.getElementById('tree');
        var SVGWidth = this.svg.viewBox.baseVal.width;
        var SVGHeight = this.svg.viewBox.baseVal.height;

        this.data.forEach(function (item, index) {
            var countLevel = 0;
            var nChild;

            // Find all cound levels and number this item in level
            self.data.forEach(function (el) {
                if (el.level == item.level) countLevel++;
                if (el == item) nChild = countLevel;
            });

            var radius = (SVGWidth / countLevel) / 2 - 2;
            radius = radius > 10 ? 10 : (radius < 3 ? 3 : radius);

            // Coordinate Y
            var cY = (item.level - 1 - self.corrLevel) * 23 + radius;

            // Increase SVG height
            var minPoint = Math.ceil(cY + radius);
            if (minPoint > SVGHeight) {
                self.svg.viewBox.baseVal.height = minPoint;
                SVGHeight = minPoint;
            }
            //console.log(minPoint,SVGHeight);

            // Coordinate X
            var cX = nChild * (SVGWidth / (countLevel + 1));

            self.data[index].cX = cX.toFixed(2);
            self.data[index].cY = cY.toFixed(2);
            self.data[index].radius = radius.toFixed(2);

            // Conver to int
            self.data[index].level = +item.level;
            self.data[index].left_key = +item.left_key;
            self.data[index].right_key = +item.right_key;

        });

        // Draw all lines
        this.data.forEach(function (item) { self.drawLine(item) });

        // Draw all items
        this.data.forEach(function (item) { self.drawItem(item) });
    },
    drawLine: function (item) {

        var parent = this.getParent(item);

        if (!parent) return false;

        var lineParams = {
            x1: item.cX,
            y1: item.cY,
            x2: parent.cX,
            y2: parent.cY
        }

        var line = document.createElementNS("http://www.w3.org/2000/svg", "line");
        line.setAttribute('x1', lineParams.x1);
        line.setAttribute('y1', lineParams.y1);
        line.setAttribute('x2', lineParams.x2);
        line.setAttribute('y2', lineParams.y2);
        line.setAttribute('stroke-width', 1);
        line.setAttribute('stroke', '#95a5a6');
        this.svg.appendChild(line);
    },
    drawItem: function (item) {
        var circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
        circle.setAttribute('cx', item.cX);
        circle.setAttribute('cy', item.cY);
        circle.setAttribute('r', item.radius);
        circle.setAttribute('fill', '#3498db');
        this.svg.appendChild(circle);

        var text = document.createElementNS("http://www.w3.org/2000/svg", "text");
        text.setAttribute('x', item.cX);
        text.setAttribute('y', item.cY);
        text.setAttribute('dy', item.radius / 3);
        text.setAttribute('fill', '#fff');
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('font-size', item.radius);
        text.setAttribute('font-family', 'Arial');
        text.innerHTML = '<a xlink:href="/thread/' + item.id + '" fill="#fff">' + item.id + '</a>';
        //text.innerHTML = item.left_key + ' - ' + item.right_key;
        this.svg.appendChild(text);
    },
    getParent: function (item) {
        var parent;
        this.data.forEach(function (el) {
            if (el.level == (item.level - 1) && el.left_key < item.left_key && el.right_key > item.right_key) {
                parent = el;
            }
        });
        return parent;
    }
}