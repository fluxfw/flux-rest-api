await (await fetch("?/routes")).json();

await (await fetch("?/example/get")).json();

await (await fetch("?/example/post", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify({
        name: "My Name",
        count: 4
    })
})).json();

await (await fetch("?/example/params/1234/abcd&query_param=5678")).json();

await (await fetch("?/example/proxy")).json();
