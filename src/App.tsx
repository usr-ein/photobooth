import {useState} from "react"

import reactLogo from "./assets/react.svg"
import viteLogo from "/vite.svg"

function App() {
    const [count, setCount] = useState(0)

    const {boothName} = parseCurrentUrl()
    console.log("boothName", boothName)

    return (
        <>
            <div>
                <a href="https://vite.dev" target="_blank">
                    <img src={viteLogo} className="logo" alt="Vite logo" />
                </a>
                <a href="https://react.dev" target="_blank">
                    <img
                        src={reactLogo}
                        className="logo react"
                        alt="React logo"
                    />
                </a>
            </div>
            <h1>Vite + React</h1>
            <div className="card">
                <button onClick={() => setCount((count) => count + 1)}>
                    count is {count}
                </button>
                <p>
                    Edit <code>src/App.tsx</code> and save to test HMR
                </p>
            </div>
            <p className="read-the-docs">
                Click on the Vite and React logos to learn more
            </p>
        </>
    )
}

type URLParams = {
    boothName: string
}

const parseCurrentUrl = (): URLParams => {
    // url is /booth/:boothName
    const url = window.location.pathname
    const params = url.split("/")
    const boothName = params[params.length - 1]
    return {boothName}
}

export default App
