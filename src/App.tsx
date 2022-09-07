import React, { useState } from 'react';
import * as uuid from 'uuid';
import './App.css';
import LoadingSpinner from './LoadingSpinner';

interface BtcKey {
  addr: string,
  pkey: string,
}

function App() {

  const [countReq, setCountReq] = useState(0)
  const [error, setError] = useState(null)
  const [btcKeys, setBtcKeys] = useState<BtcKey[]>([])
  const [loading, setLoading] = useState<number[]>([])

  let classButton = countReq > 2 ? 'apple-button apple-button-disabled' : 'apple-button'


  function clickHandler() {
    setError(null)
    setCountReq(prev => prev + 1)
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
      ])),
      (error) => {
        setError(error)
        console.log(error.message)
      }
      )
      .catch((error) => {
        setError(error)
        console.log(error.message)
      })
      .finally(() => {
        setCountReq(prev => prev - 1)
        setLoading(prevTasks => prevTasks.slice(0, prevTasks.length - 1))
      });
  }

  return (
    <>
      <div className="App">
        <header className="App-header">
          <button onClick={clickHandler} className={classButton} disabled={countReq > 2 ? true : false}>
            Fetch address
          </button>
          <div className='keys-block'>
            {!error
            ?
              btcKeys.map((key) => (

                <div className='address' key={uuid.v4()}>
                  {
                    (key.addr === undefined)
                      ?
                      (<p className='address'>something went wrong, please try again</p>)
                      :
                      (`address: ${key.addr}`)
                  }
                  <hr />
                  <br />
                </div>

              ))
              :
              <p className='address'>Sorry,big fail, please try again</p>
            }
          </div>
          <div>
            {
              loading.map(() => (
                <LoadingSpinner key={uuid.v4()} />
              ))
            }
          </div>
        </header>
      </div>
    </>
  );
}

export default App;
