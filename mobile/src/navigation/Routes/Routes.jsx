import React from 'react'
import { createStackNavigator } from '@react-navigation/stack'
import { NavigationContainer } from '@react-navigation/native'
import {
  PasswordForgetScreen, Sent, SignInScreen,
} from '../../screens'
import { Menu } from '../Menu'

const RootStack = createStackNavigator()

export const Routes = () => {
  const [isAuthenticated, setIsAuthenticated] = React.useState(false)

  const handleSignIn = () => {
    setIsAuthenticated(true)
  }

  const handleSignOut = () => {
    setIsAuthenticated(false)
  }

  return (
    <NavigationContainer>
      <RootStack.Navigator>
        {isAuthenticated ? (
          <RootStack.Screen
            name="App"
            component={Menu}
            options={{ headerShown: false }}
            handleSignOut={handleSignOut}
          />
        ) : (
          <>
            <RootStack.Screen
              name="Sign In"
              options={{ headerShown: false }}
            >
              {(props) => (
                // eslint-disable-next-line react/jsx-props-no-spreading
                <SignInScreen {...props} onSignIn={handleSignIn} />
              )}
            </RootStack.Screen>
            <RootStack.Screen
              name="Восстановление пароля"
              component={PasswordForgetScreen}
            />
            <RootStack.Screen
              name="isSent"
              component={Sent}
              options={{ headerShown: false }}
            />
          </>
        )}
      </RootStack.Navigator>
    </NavigationContainer>
  )
}
