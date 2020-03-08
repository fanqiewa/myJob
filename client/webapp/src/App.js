import React from 'react';
import {
  BrowserRouter as Router,
  Route,
  Switch
} from 'react-router-dom';

import Routers from './router/routerMap';
import NotFound from './components/notFound/notFound';

function App() {
  return (
    <Router>
      <div className="App">
        <Switch>
          {
            Routers.map((item, index) => {
              return <Route key={index} path={item.path} exact render={(props) => (<item.component {...props} />)} />
            })
          } 
          {/* 所有错误路由跳转页面 */}
         <Route component={NotFound} />
        </Switch>
      </div>
    </Router>
  );
}

export default App;
