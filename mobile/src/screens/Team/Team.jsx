import {Image, Text, TouchableHighlight, View, Linking} from "react-native";
import React from "react";
import { COLORS } from "../../../constants";
import { Layout } from "../../shared";
import { Button } from "react-native-paper";
import {CallHistory} from "../CallHistory";

const img = '../../../assets/reload.png'


export const Team = () => {
  return (
    <Layout>
      <Text style={styles.text}>Бригада еще не сформирована</Text>
      <Image source={require(img)} style={styles.img}/>

      <View style={styles.btnHolder}>
        <Button mode="outlined" raised>Бригада не готова к дежурству</Button>
        <Button mode="contained" style={styles.btn}>Бригада вышла на дежурство</Button>
      </View>
    </Layout>
  );
}

const styles = {
  text: {
    fontSize: 30,
    color: COLORS.black,
  },
  img: {
    width: 48,
    height: 48,
    marginLeft: 'auto',
    marginRight: 'auto',
    marginTop: 24
  },
  btnHolder: {
    marginTop: 'auto',
  },
  btn: {
    marginTop: 16
  }
}
