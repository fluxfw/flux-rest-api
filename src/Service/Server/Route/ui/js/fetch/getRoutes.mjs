import {fetchResponseHelper} from "./fetchResponseHelper.mjs";

const __dirname = import.meta.url.substring(0, import.meta.url.lastIndexOf("/"));

export async function getRoutes() {
    return (await fetchResponseHelper(await fetch(`${__dirname}/../../..`, {
        headers: {
            Accept: "application/json"
        }
    }))).json();
}
