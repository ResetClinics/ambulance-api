import * as React from 'react';
import { Button } from 'react-native-paper';
import {COLORS} from "../../../constants";

export const Btn = ({ children, secondary }) => {
  if (secondary) {
    return (
      <Button mode="contained" onPress={() => console.log('Pressed')} buttonColor={COLORS.primary}  dark={true}>
        {children}
      </Button>
    )
  }

  return (
    <Button mode="contained" onPress={() => console.log('Pressed')} buttonColor='transparent' textColor={COLORS.primary} dark={false} style={styles.primary}>
      {children}
    </Button>
  )
}

const styles = {
  primary: {
    borderColor: COLORS.primary,
    borderWidth: 2,
    borderRadius: 4,
  }
}

