let currentPath = [];

async function fetchGenealogyData() {
    try {
        const response = await fetch('/api/genealogy/tree');
        if (!response.ok) throw new Error('Failed to fetch tree');
        
        const data = await response.json();
        currentPath = [data]; // Set the root of the path to the logged-in user
        render();
    } catch (error) {
        document.getElementById('tree-display').innerHTML = 
            `<div class="empty-msg" style="color: red;">Error: ${error.message}</div>`;
    }
}

function render() {
    if (currentPath.length === 0) return;

    const currentRoot = currentPath[currentPath.length - 1];
    
    // Render Breadcrumbs
    const bc = document.getElementById('breadcrumb-trail');
    bc.innerHTML = currentPath.map((node, index) => {
        // If index is 0, it is the logged-in user
        const displayName = index === 0 ? `${node.name} (You)` : node.name;
        return `<span class="crumb" onclick="goToPath(${index})">${displayName}</span>`;
    }).join('<span class="separator">/</span>');

    // Render Tree
    const display = document.getElementById('tree-display');
    
    display.innerHTML = `
        <div class="node">
            <strong>${currentRoot.name} (ID: ${currentRoot.id})</strong>
            <div class="meta">Direct Downlines: ${currentRoot.children ? currentRoot.children.length : 0}</div>
        </div>
        <div class="children-grid">
            ${(currentRoot.children && currentRoot.children.length > 0) ? 
                currentRoot.children.map(child => `
                    <div class="node" onclick="drillDown(${child.id})">
                        ${child.name}
                        <div class="meta">Relative Depth: ${child.depth}</div>
                    </div>
                `).join('') : 
                '<div class="empty-msg">No further downlines</div>'
            }
        </div>
    `;
}

function drillDown(id) {
    const currentRoot = currentPath[currentPath.length - 1];
    const nextNode = currentRoot.children.find(c => c.id === id);
    if (nextNode) {
        currentPath.push(nextNode);
        render();
    }
}

function goToPath(index) {
    currentPath = currentPath.slice(0, index + 1);
    render();
}

fetchGenealogyData();