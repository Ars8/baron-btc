import React, { useState } from 'react';
import './App.css';

interface BtcKey {
  addr: string,
  pkey: string,
}

function App() {

  const [countReq, setCountReq] = useState(0)
  const [error, setError] = useState(null)
  const [btcKeys, setBtcKeys] = useState<BtcKey[]>([])
  const [loading, setLoading] = useState<number[]>([])
  const [isLoaded, setIsLoaded] = useState(true)

  let classButton = countReq > 2 ? 'apple-button apple-button-disabled' : 'apple-button'


  function clickHandler() {
    setCountReq(prev => prev + 1)
    setIsLoaded(false)
    setLoading(prev => [...prev, 1])

    fetch('http://btcaddr.ru/btcaddr.php', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
    })
      .then(response => response.json())
      .then((response) => (setBtcKeys((prevKeys) => [
        ...prevKeys,
        {
          addr: response.addr,
          pkey: response.pkey,
        },
      ])))
      .catch((error) => {
        setError(error)
        console.log(error)
      })
      .finally(() => {
        setIsLoaded(true)
        setCountReq(prev => prev - 1)
        setLoading(prevTasks => prevTasks.slice(0, prevTasks.length-1))
      });
  }

  //console.log(btcKeys)
  //console.log(error)
  //console.log(countReq)
  //console.log(loading)
  //console.log(isLoaded)

  


  return (
    <>
      <div className="App">
        <header className="App-header">
          <button onClick={clickHandler} className={classButton} disabled={countReq > 2 ? true : false}>Request</button>
          <div className='keys-block'>
            {
              btcKeys.map((key) => (

                <div className='address' key={key.pkey}>{key.addr === undefined ? <p className='address'>something went wrong, please try again</p> : `address: ${key.addr}`}<hr /><br /></div>
              
              ))
            }
          </div>
          <div>
            {
              loading.map((index) => (
                <div key={index}>...Loading...</div>
              ))              
            }
          </div>

        </header>
      </div>
    </>
  );
}

export default App;
